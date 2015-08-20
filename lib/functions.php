<?php
/**
 * Plugin related functions are bundled here
 */

/**
* Returns a where clause for a search query.
*
* Search Advanced: added the ability to use a wildcard in full text search
*
* @param string  $table        Prefix for table to search on
* @param array   $fields       Fields to match against
* @param array   $params       Original search params
* @param boolean $use_fulltext Toggle the use of full text search
*
* @return string
*/
function search_advanced_get_where_sql($table, $fields, $params, $use_fulltext = TRUE) {
	$query = (array) search_advanced_query_to_array(elgg_extract('query', $params, ''));
	
	if (empty($query) || empty($fields)) {
		return '';
	}
	
	$likes = [];
	foreach ($fields as $field) {
		
		if ($table) {
			// add the table prefix to the fields
			$field = "$table.$field";
		}
		
		$field_likes = [];
		foreach ($query as $query_part) {
			$query_part = sanitise_string($query_part);
		
			$field_likes[] = "$field LIKE '%$query_part%'";
		}
		$likes[] = "(" . implode(' AND ', $field_likes) . ")";
	}
	$likes_str = implode(' OR ', $likes);
	$where = "($likes_str)";

	return $where;
}

/**
 * Function to register menu items on the search result page
 *
 * @param array $params parameters to be used in this function
 *
 * @return void
 */
function search_advanced_register_menu_items($params) {
	$types = elgg_extract('types', $params, []);
	$custom_types = elgg_extract('custom_types', $params, []);
	$search_params = elgg_extract('params', $params, []);
	$search_result_counters = elgg_extract('search_result_counters', $params, []);
	
	$query_parts = [
		'q' => $search_params['query'],
		'search_type' => 'all',
	];
	
	$query_data = htmlspecialchars(http_build_query($query_parts));
	
	elgg_register_menu_item('page', [
		'name' => 'all',
		'text' => elgg_echo('all'),
		'href' => "search?$query_data"
	]);
	
	$query_parts['owner_guid'] = $search_params['owner_guid'];
	$query_parts['container_guid'] = $search_params['container_guid'];
	
	foreach ($search_result_counters as $type_subtype => $count) {
		$label = $type_subtype;
		
		list($item, $type, $subtype) = explode(':', $type_subtype);
		if ($item == 'item') {
			// entities search
			$query_parts['entity_subtype'] = $subtype;
			$query_parts['entity_type'] = $type;
			$query_parts['search_type'] = 'entities';
			
		} else {
			// custom searches
			$query_parts['search_type'] = $type;
		}
		
		$text = elgg_echo($label) . ' ' . elgg_format_element('span', ['class' => 'elgg-quiet'], "({$count})");
		
		$data = htmlspecialchars(http_build_query($query_parts));
			
		elgg_register_menu_item('page', [
			'name' => $label,
			'text' => $text,
			'href' => "search?$data",
			'section' => ($type == 'object') ? $type : 'default'
		]);
	}
}

/**
 * Returns an array of counters
 *
 * @param array $params parameters
 *
 * @return array
 */
function search_advanced_get_combined_search_counters($params) {
	$result = [];
	
	$search_params = elgg_extract('search_params', $params);
	
	$db_prefix = elgg_get_config('dbprefix');
		
	$count_query  = "SELECT es.subtype, count(distinct e.guid) as total";
	$count_query .= " FROM {$db_prefix}entities e";
	$count_query .= " JOIN {$db_prefix}objects_entity oe ON e.guid = oe.guid";
	$count_query .= " JOIN {$db_prefix}entity_subtypes es ON e.subtype = es.id";
	
	$fields = ['title', 'description'];
	$where = search_advanced_get_where_sql('oe', $fields, $search_params);
	
	// add tags search
	if ($valid_tag_names = elgg_get_registered_tag_metadata_names()) {
		$tag_name_ids = array();
		foreach ($valid_tag_names as $tag_name) {
			$tag_name_ids[] = elgg_get_metastring_id($tag_name);
		}
			
		$count_query .= " JOIN {$db_prefix}metadata md on e.guid = md.entity_guid";
		$count_query .= " JOIN {$db_prefix}metastrings msv ON md.value_id = msv.id";
			
		$md_where = "((md.name_id IN (" . implode(",", $tag_name_ids) . ")) AND msv.string = '" . sanitise_string($search_params["query"]) . "')";
	}
	
	// add wheres
	$count_query .= " WHERE e.type = 'object' AND es.subtype IN ('" . implode("', '", $search_params['subtype']) . "') AND ";
	if ($search_params['container_guid']) {
		$count_query .= "e.container_guid = " . $search_params['container_guid'] . " AND ";
	}
	
	if (isset($md_where)) {
		$count_query .= "((" . $where . ") OR (" . $md_where . "))";
	} else {
		$count_query .= $where;
	}
	
	$count_query .= " AND e.site_guid = " . elgg_get_site_entity()->getGUID() . " AND ";
	
	// Add access controls
	$count_query .= get_access_sql_suffix('e');
	$count_query .= " GROUP BY e.subtype";
	
	$totals = get_data($count_query);
	
	return $totals;
}

function search_advanced_query_to_array($query, $delimiter = '\s') {
	if (empty($query)) {
		return $query;
	}
	
	$matches = [];
	
	$pattern = '/"+(.+?)"+|(?<![\S"])([^"' . $delimiter . ',]+)(?!["])/';

	if (!preg_match_all($pattern, $query, $matches)) {
		return $query;
	}
	
	$result = $matches[0];
	
	foreach ($result as $key => $val) {
		$val = trim(trim($val, "\""));
		
		if (empty($val)) {
			// remove empty values
			unset($result[$key]);
			continue;
		}
		
		$result[$key] = $val;
	}
	
	// remove duplicates
	$result = array_unique($result);

	return array_values($result);
}

function search_advanced_tag_query_to_array($query) {
	if (empty($query)) {
		return $query;
	}
	
	if (elgg_get_plugin_setting("enable_multi_tag", "search_advanced") !== "yes") {
		return $query;
	}

	$separator = '\s';
	if (elgg_get_plugin_setting("multi_tag_separator", "search_advanced", "comma") == 'comma') {
		$separator = ',';
	}
	
	return search_advanced_query_to_array($query, $separator);
}

function search_advanced_get_search_url() {
	return current_page_url();
}

function search_advanced_update_list_type() {

	// check the user preference for list_type preference change
	if (!elgg_is_logged_in()) {
		return;
	}
	
	$list_type = get_input('list_type');
	if (empty($list_type)) {
		return;
	}
	
	$current_list_type = elgg_get_plugin_user_setting('list_type', elgg_get_logged_in_user_guid(), 'search_advanced');
	if ($current_list_type !== $list_type) {
		// save new preference
		elgg_set_plugin_user_setting('list_type', $list_type, elgg_get_logged_in_user_guid(), 'search_advanced');
	}
}

function search_advanced_get_list_type() {
	// return the 'active' entity view type to be used in views
	$result = 'entity';
	
	if (elgg_is_logged_in()) {
		$user_setting = elgg_get_plugin_user_setting('list_type', elgg_get_logged_in_user_guid(), 'search_advanced');
		if ($user_setting) {
			$result = $user_setting;
		}
	} else {
		$result = get_input('list_type', $result);
	}
	
	return $result;
}

/**
 * Passes results, and original params to the view functions for
 * search type.
 *
 * @param array $results
 * @param array $params
 * @param string $view_type = list, entity or layout
 * @return string
 */
function search_advanced_get_search_view($params, $view_type) {
	if (in_array($view_type, ['entity', 'list', 'layout'])) {
		return search_get_search_view($params, $view_type);
	}
	
	$list_type = search_advanced_get_list_type();
		
	$view_order = [];
	
	if (isset($params['type']) && $params['type'] && isset($params['subtype']) && $params['subtype']) {
		$view_order[] = "search/{$params['type']}/{$params['subtype']}/$list_type";
	}
	
	// also check for the default type
	if (isset($params['type']) && $params['type']) {
		$view_order[] = "search/{$params['type']}/$list_type";
	}
	
	// check search types
	if (isset($params['search_type']) && $params['search_type']) {
		$view_order[] = "search/{$params['search_type']}/$list_type";
	}
	
	// finally default to a search list default
	$view_order[] = "search/$list_type";
	
	foreach ($view_order as $view) {
		if (elgg_view_exists($view)) {
			return $view;
		}
	}
	
	return search_get_search_view($params, 'entity');
}

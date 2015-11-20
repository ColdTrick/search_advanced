<?php
/**
 * Plugin related functions are bundled here
 */

/**
 * Function to (un)register various search hooks
 */
function search_advanced_prepare_search_hooks() {
	// unregister default search hooks
	elgg_unregister_plugin_hook_handler('search', 'object', 'search_objects_hook');
	elgg_unregister_plugin_hook_handler('search', 'user', 'search_users_hook');
	elgg_unregister_plugin_hook_handler('search', 'group', 'search_groups_hook');
	elgg_unregister_plugin_hook_handler('search', 'tags', 'search_tags_hook');
	
	// register search advanced search hooks
	elgg_register_plugin_hook_handler('search', 'object', 'search_advanced_objects_hook');
	elgg_register_plugin_hook_handler('search', 'user', 'search_advanced_users_hook');
	elgg_register_plugin_hook_handler('search', 'group', 'search_advanced_groups_hook');
	elgg_register_plugin_hook_handler('search', 'combined:all', 'search_advanced_combined_all_hook');
	
	elgg_register_plugin_hook_handler('search_types', 'get_types', 'search_advanced_custom_types_hook', 9999);
	
	// register fallback to default search hooks
	elgg_register_plugin_hook_handler('search', 'object', 'search_advanced_fallback_search_hook', 9000);
	elgg_register_plugin_hook_handler('search', 'user', 'search_advanced_fallback_search_hook', 9000);
	elgg_register_plugin_hook_handler('search', 'group', 'search_advanced_fallback_search_hook', 9000);
	elgg_register_plugin_hook_handler('search', 'tags', 'search_advanced_fallback_search_hook', 9000);

}

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
	$search_params = elgg_extract('params', $params, []);
	$search_result_counters = elgg_extract('search_result_counters', $params, []);
	
	$query_parts = [
		'q' => $search_params['query'],
		'search_type' => 'all',
		'container_guid' => elgg_extract('container_guid', $search_params),
	];
	
	$query_data = htmlspecialchars(http_build_query($query_parts));
	
	elgg_register_menu_item('search_types', [
		'name' => 'all',
		'text' => elgg_echo('all'),
		'href' => "search?$query_data"
	]);
	
	$query_parts['owner_guid'] = elgg_extract('owner_guid', $search_params);
	
	$searched_search_type = elgg_extract('search_type', $search_params);
	$searched_type = elgg_extract('type', $search_params);
	$searched_subtype = elgg_extract('subtype', $search_params);
	
	if ($searched_search_type == 'entities') {
		$searched_search_type = 'item';
	} else {
		$searched_type = $searched_search_type;
		$searched_search_type = 'search_types';
	}
	
	$searched_typesubtype = rtrim(implode(':', [$searched_search_type, $searched_type, $searched_subtype]), ':');
	
	if (!array_key_exists($searched_typesubtype, $search_result_counters) && ($searched_typesubtype !== 'search_types:all')) {
		$search_result_counters[$searched_typesubtype] = 0;
	}
	
	foreach ($search_result_counters as $type_subtype => $count) {
		if (!$count && ($type_subtype !== $searched_typesubtype)) {
			continue;
		}
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
			$query_parts['entity_subtype'] = null;
			$query_parts['entity_type'] = null;
		}
		
		$text = elgg_echo($label) . ' ' . elgg_format_element('span', ['class' => 'elgg-quiet'], "({$count})");

		$data = htmlspecialchars(http_build_query($query_parts));
				
		elgg_register_menu_item('search_types', [
			'name' => $label,
			'text' => $text,
			'href' => "search?$data",
			'data' => ['count' => $count],
			'selected' => ($type_subtype == $searched_typesubtype),
			'section' => ($type == 'object') ? $type : 'default'
		]);
	}
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

function search_advanced_get_metastring_ids($strings, $case_sensitive = true) {
	if (!is_array($strings)) {
		return false;
	}
	
	$strings = array_map('sanitise_string', $strings);
	
	$strings = implode("', '", $strings);
	
	$dbprefix = elgg_get_config('dbprefix');
	
	if ($case_sensitive) {
		$query = "SELECT * FROM {$dbprefix}metastrings WHERE BINARY string IN ('{$strings}')";
	} else {
		$query = "SELECT * FROM {$dbprefix}metastrings WHERE string IN ('{$strings}')";
	}
		
	$results = get_data($query, function($row) {
		return $row->id;
	});
	
	return $results;
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

function search_advanced_get_search_url($query_elements = []) {
	$result = current_page_url();
	
	if (!empty($query_elements)) {
		$result = elgg_http_add_url_query_elements($result, $query_elements);
	}
	
	return $result;
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

function search_advanced_search_get_types() {
	$types = get_registered_entity_types();
	
	$object_types = elgg_extract('object', $types);
	if ($object_types) {
		// the sidebar menu shows objects below other entity types
		// by moving the object types to the end of the array this will also
		// make sure that on the search index page they are also listed last
		unset($types['object']);
		$types['object'] = $object_types;
	}
	
	return $types;
}

function search_advanced_search_index_custom_search($type, $params, $combine_search_results = 'no') {
	if (empty($type)) {
		return;
	}
	
	$current_search_type = elgg_extract('search_type', $params);
	
	$current_params = $params;
	$current_params['search_type'] = $type;
	$current_params['subtype'] = ELGG_ENTITIES_ANY_VALUE;
	$current_params['type'] = ELGG_ENTITIES_ANY_VALUE;
	
	if (($current_search_type !== 'all') && ($current_search_type !== $type)) {
		// only want count if doing specific search
		$current_params['count'] = true;
	}
	
	if (($combine_search_results === 'all') && ($current_search_type == 'all')) {
		// content comes from somewhere else
		$current_params['count'] = true;
	}
	
	// assumed result to be ['count' => xxx, 'entities' => [ElggEntity entity A, ElggEntity entity B]]
	$result = elgg_trigger_plugin_hook('search', $type, $current_params, []);
		
	if ($result === FALSE) {
		// someone is saying not to display these types in searches.
		return;
	}
	
	if (isset($result['content'])) {
		// some special case where content is provide via a hook instead of a view
		return $result;
	}
	
	if ($current_params['count'] == true) {
		return $result;
	}
	
	// are there entities?
	$entities = elgg_extract('entities', $result);
	if (!is_array($entities)) {
		return $result;
	}
	
	$view = search_get_search_view($current_params, 'list');
	if (empty($view)) {
		// nothing to see...
		return $result;
	}
		
	$result['content'] = elgg_view($view, [
		'results' => $result,
		'params' => $current_params,
	]);
	
	return $result;
}

function search_advanced_search_index_combined_search($combine_search_results = 'no', $params) {
	
	if (elgg_extract('search_type', $params) !== 'all') {
		return;
	}
	
	if (empty($params['query'])) {
		return;
	}
	
	if (!in_array($combine_search_results, ['all', 'objects'])) {
		return;
	}
	
	$types = search_advanced_search_get_types();
	if (isset($types['object']) && in_array('groupforumtopic', $types['object'])) {
		$types['object'][] = 'discussion_reply';
	}
	if (isset($types['object']) && in_array('page', $types['object'])) {
		$types['object'][] = 'page_top';
	}
	
	$current_params = $params;
	$current_params['search_type'] = 'entities';
	
	if ($combine_search_results == 'objects') {
		$current_params['type'] = 'object';
		$current_params['subtype'] = elgg_extract('object', $types);
		
		// show a bit more content but disable pagination
		$current_params['offset'] = 0;
		$current_params['limit'] = 20;
				
		if (empty($current_params['subtype'])) {
			return;
		}
		
		$results = elgg_trigger_plugin_hook('search', 'object', $current_params, []);
		
		// reset count to 0 to remove the "view more" url
		$results['count'] = 0;
	} elseif ($combine_search_results == 'all') {
		unset($current_params['type']);
		unset($current_params['subtype']);
		
		$current_params['limit'] = (int) get_input('limit', 10);
		$current_params['offset'] = (int) get_input('offset', 0);
		$current_params['pagination'] = true;
		
		foreach ($types as $type => $subtypes) {
			if (empty($subtypes)) {
				$types[$type] = null;
			}
		}
		
		$current_params['type_subtype_pairs'] = $types;
		$results = elgg_trigger_plugin_hook('search', 'combined:all', $current_params, []);
	}
	
	$entities = elgg_extract('entities', $results);
	
	if (empty($entities)) {
		return;
	}
	
	elgg_push_context('combined_search');
	
	$view = search_get_search_view($current_params, 'list');
	if (empty($view)) {
		return;
	}

	$content = elgg_view($view, [
		'results' => $results,
		'params' => $current_params,
	]);
	
	elgg_pop_context();
	
	return ['content' => $content, 'count' => $results['count']];
}

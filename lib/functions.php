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
	$query = elgg_extract("query", $params, "");
	if (empty($query)) {
		return "";
	}
	
	$query_array = explode(" ", $query);
	
	if (count($query_array) > 1) {
		$multi_query = array();
		foreach ($query_array as $value) {
			$temp_field = trim($value);
			if (!empty($temp_field)) {
				$multi_query[] = $temp_field;
			}
		}
		
		if (count($multi_query) > 1) {
			$query = $multi_query;
		}
	}
	
	// add the table prefix to the fields
	if ($table) {
		foreach ($fields as $i => $field) {
			$fields[$i] = "$table.$field";
		}
	}
	
	if (!is_array($query)) {
		$query = array($query);
	}

	$likes = array();
	foreach ($fields as $field) {
		$field_likes = array();
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

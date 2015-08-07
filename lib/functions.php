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
	$search_params = elgg_extract('search_params', $params, []);
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

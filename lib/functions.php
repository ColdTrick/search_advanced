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

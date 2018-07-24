<?php
/**
 * Plugin related functions are bundled here
 */

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
	if (elgg_get_plugin_setting("multi_tag_separator", "search_advanced") == 'comma') {
		$separator = ',';
	}
	
	return search_advanced_query_to_array($query, $separator);
}

function search_advanced_get_list_type() {
	// return the 'active' entity view type to be used in views
	$result = 'list';
	
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

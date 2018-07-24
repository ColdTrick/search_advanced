<?php
/**
 * Plugin related functions are bundled here
 */

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

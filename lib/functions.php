<?php
/**
 * Plugin related functions are bundled here
 */

function search_advanced_get_list_type() {
	// return the 'active' entity view type to be used in views
	
	if (elgg_is_logged_in()) {
		return elgg_get_plugin_user_setting('list_type', elgg_get_logged_in_user_guid(), 'search_advanced', 'list');
	} else {
		return get_input('list_type', 'list');
	}
}

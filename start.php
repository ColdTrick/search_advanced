<?php

require_once(dirname(__FILE__) . "/lib/functions.php");
require_once(dirname(__FILE__) . "/lib/hooks.php");

/**
 * Initializes the plugin
 * 
 * @return void
 */
function search_advanced_init(){
	// page handler for search actions and results
	elgg_register_page_handler('search_advanced', 'search_advanced_page_handler');
	elgg_register_page_handler('search', 'search_advanced_search_page_handler');
	
	// search hooks
	search_advanced_unregister_default_search_hooks();
	search_advanced_register_search_hooks();
	
	// views
	elgg_extend_view("css/elgg", "search_advanced/css/site");
	elgg_extend_view("js/elgg", "search_advanced/js/site");
	
	// search widget
	elgg_register_widget_type("search", elgg_echo("search"), elgg_echo("search"), "profile,dashboard,index,groups", true);
	
	if (elgg_is_active_plugin("categories")) {
		// make universal categories searchable
		add_translation(get_current_language(), array("tag_names:universal_categories" => elgg_echo("categories")));
		elgg_register_tag_metadata_name("universal_categories");
	}
	
	// actions
	elgg_register_action("search_advanced/settings/save", dirname(__FILE__) . "/actions/plugins/settings/save.php", "admin");
}

/**
 * Handles search pages
 * 
 * @param array $page page segments
 * 
 * @return boolean
 */
function search_advanced_page_handler($page){
	switch ($page[0]) {
		case "autocomplete":
			include_once(dirname(__FILE__) . "/procedures/autocomplete.php");
			return true;
	}
}

/**
 * Handles search advanced pages
 * 
 * @param array $page page segments
 * 
 * @return boolean
 */
function search_advanced_search_page_handler($page){
	// if there is no q set, we're being called from a legacy installation
	// it expects a search by tags.
	// actually it doesn't, but maybe it should.
	// maintain backward compatibility
	if (!get_input('q', get_input('tag', NULL)) && isset($page[0])) {
		set_input('q', $page[0]);
		//set_input('search_type', 'tags');
	}
	
	// as there is no tags search any more, replace it with ALL search
	if (get_input("search_type") == "tags") {
		set_input("search_type", "all");
	}
	
	$base_dir = elgg_get_plugins_path() . 'search_advanced/pages/search';
	
	include_once("$base_dir/index.php");
	return true;
}

elgg_register_event_handler('init','system','search_advanced_init');

// hooks and events to clear cache
// register hooks
elgg_register_plugin_hook_handler("action", "admin/plugins/activate", "search_advanced_clear_keywords_cache");
elgg_register_plugin_hook_handler("action", "admin/plugins/deactivate", "search_advanced_clear_keywords_cache");
elgg_register_plugin_hook_handler("action", "admin/plugins/activate_all", "search_advanced_clear_keywords_cache");
elgg_register_plugin_hook_handler("action", "admin/plugins/deactivate_all", "search_advanced_clear_keywords_cache");
elgg_register_plugin_hook_handler("action", "plugins/settings/save", "search_advanced_clear_keywords_cache");

// register events
elgg_register_event_handler("upgrade", "system", "search_advanced_clear_keywords_cache");

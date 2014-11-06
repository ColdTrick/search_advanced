<?php

require_once(dirname(__FILE__) . "/lib/functions.php");
require_once(dirname(__FILE__) . "/lib/hooks.php");
require_once(dirname(__FILE__) . "/lib/page_handlers.php");

// register default Elgg event
elgg_register_event_handler("init","system","search_advanced_init");

/**
 * Initializes the plugin
 *
 * @return void
 */
function search_advanced_init() {
	// page handler for search actions and results
	elgg_register_page_handler("search_advanced", "search_advanced_page_handler");
	elgg_register_page_handler("search", "search_advanced_search_page_handler");
	
	// search hooks
	search_advanced_unregister_default_search_hooks();
	search_advanced_register_search_hooks();
	
	// views
	elgg_extend_view("css/elgg", "css/search_advanced/site");
	elgg_extend_view("js/elgg", "js/search_advanced/site");
	
	// widgets
	elgg_register_widget_type("search", elgg_echo("search"), elgg_echo("search"), array("profile", "dashboard", "index", "groups"), true);
	elgg_register_widget_type("search_user", elgg_echo("search_advanced:widgets:search_user:title"), elgg_echo("search_advanced:widgets:search_user:description"), array("dashboard", "index"));
	
	if (elgg_is_active_plugin("categories")) {
		// make universal categories searchable
		add_translation(get_current_language(), array("tag_names:universal_categories" => elgg_echo("categories")));
		elgg_register_tag_metadata_name("universal_categories");
	}
	
	// hooks and events to clear cache
	// register hooks
	elgg_register_plugin_hook_handler("action", "admin/plugins/activate", "search_advanced_clear_keywords_cache");
	elgg_register_plugin_hook_handler("action", "admin/plugins/deactivate", "search_advanced_clear_keywords_cache");
	elgg_register_plugin_hook_handler("action", "admin/plugins/activate_all", "search_advanced_clear_keywords_cache");
	elgg_register_plugin_hook_handler("action", "admin/plugins/deactivate_all", "search_advanced_clear_keywords_cache");
	elgg_register_plugin_hook_handler("action", "plugins/settings/save", "search_advanced_clear_keywords_cache");

	elgg_register_plugin_hook_handler("register", "menu:search_type_selection", "search_advanced_register_menu_type_selection");
	
	// register events
	elgg_register_event_handler("upgrade", "system", "search_advanced_clear_keywords_cache");
	
	// actions
	elgg_register_action("search_advanced/settings/save", dirname(__FILE__) . "/actions/plugins/settings/save.php", "admin");
}

<?php
/**
 * Main file for the Search Advanced plugin
 */

require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/hooks.php');
require_once(dirname(__FILE__) . '/lib/page_handlers.php');

// register default Elgg event
elgg_register_event_handler('init','system','search_advanced_init');

/**
 * Initializes the plugin
 *
 * @return void
 */
function search_advanced_init() {
	// page handler for search actions and results
	elgg_register_page_handler('search_advanced', 'search_advanced_page_handler');
	elgg_register_page_handler('search', 'search_advanced_search_page_handler');
	
	search_advanced_prepare_search_hooks();
		
	// unregister object:page_top from search
	elgg_unregister_entity_type('object', 'page_top');
	elgg_register_plugin_hook_handler('search', 'object:page', 'search_advanced_search_page');
	
	// views
	elgg_extend_view('css/elgg', 'css/search_advanced/site');
	elgg_extend_view('js/elgg', 'js/search_advanced/site.js');
	
	elgg_define_js('browserstate-history', [
		'src' => '/mod/search_advanced/vendors/browserstate-history.js/jquery.history.js',
		'exports' => 'History',
	]);
	
	elgg_extend_view('page/elements/foot', 'search_advanced/foot', 400);
		
	// widgets
	elgg_register_widget_type('search', elgg_echo('search'), elgg_echo('search'), ['profile', 'dashboard', 'index', 'groups'], true);
	elgg_register_widget_type('search_user', elgg_echo('search_advanced:widgets:search_user:title'), elgg_echo('search_advanced:widgets:search_user:description'), ['dashboard', 'index']);
		
	// register hooks
	elgg_register_plugin_hook_handler('register', 'menu:search_type_selection', 'search_advanced_register_menu_type_selection');
	elgg_register_plugin_hook_handler('register', 'menu:search_list', 'search_advanced_register_menu_list');
	
	// actions
	elgg_register_action('search_advanced/settings/save', dirname(__FILE__) . '/actions/plugins/settings/save.php', 'admin');
}

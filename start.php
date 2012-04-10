<?php

require_once(dirname(__FILE__) . "/lib/functions.php");
require_once(dirname(__FILE__) . "/lib/hooks.php");

function search_advanced_init(){
	// page handler for search actions and results
	elgg_register_page_handler('search', 'search_advanced_page_handler');
	
	// search hooks
	search_advanced_unregister_default_search_hooks();
	search_advanced_register_search_hooks();
	
	// views
	elgg_extend_view("css/elgg", "search_advanced/css/site");
	elgg_extend_view("js/elgg", "search_advanced/js/site");
}

function search_advanced_page_handler($page){
	// if there is no q set, we're being called from a legacy installation
	// it expects a search by tags.
	// actually it doesn't, but maybe it should.
	// maintain backward compatibility
	if(!get_input('q', get_input('tag', NULL))) {
		set_input('q', $page[0]);
		//set_input('search_type', 'tags');
	}
	
	$base_dir = elgg_get_plugins_path() . 'search/pages/search';
	
	include_once("$base_dir/index.php");
	return true;
}


elgg_register_event_handler('init','system','search_advanced_init');
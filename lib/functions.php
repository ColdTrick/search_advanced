<?php

function search_advanced_unregister_default_search_hooks(){
	// register some default search hooks
	elgg_unregister_plugin_hook_handler('search', 'object', 'search_objects_hook');
	elgg_unregister_plugin_hook_handler('search', 'user', 'search_users_hook');
	elgg_unregister_plugin_hook_handler('search', 'group', 'search_groups_hook');
	
	// tags and comments are a bit different.
	// register a search types and a hooks for them.
	elgg_unregister_plugin_hook_handler('search_advanced_types', 'get_types', 'search_custom_types_tags_hook');
	elgg_unregister_plugin_hook_handler('search', 'tags', 'search_tags_hook');
	
	elgg_unregister_plugin_hook_handler('search_advanced_types', 'get_types', 'search_custom_types_comments_hook');
	elgg_unregister_plugin_hook_handler('search', 'comments', 'search_comments_hook');
}
function search_advanced_register_search_hooks(){
	// register some default search hooks
	elgg_register_plugin_hook_handler('search', 'object', 'search_advanced_objects_hook');
	elgg_register_plugin_hook_handler('search', 'user', 'search_advanced_users_hook');
	elgg_register_plugin_hook_handler('search', 'group', 'search_advanced_groups_hook');
	
	// tags and comments are a bit different.
	// register a search types and a hooks for them.
	elgg_register_plugin_hook_handler('search_advanced_types', 'get_types', 'search_advanced_custom_types_tags_hook');
	elgg_register_plugin_hook_handler('search', 'tags', 'search_advanced_tags_hook');
	
	elgg_register_plugin_hook_handler('search_advanced_types', 'get_types', 'search_advanced_custom_types_comments_hook');
	elgg_register_plugin_hook_handler('search', 'comments', 'search_advanced_comments_hook');
}
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
	
	elgg_unregister_plugin_hook_handler('search_types', 'get_types', 'search_custom_types_tags_hook');
}

function search_advanced_register_search_hooks(){
	// register some default search hooks
	elgg_register_plugin_hook_handler('search', 'object', 'search_advanced_objects_hook');
	elgg_register_plugin_hook_handler('search', 'user', 'search_advanced_users_hook');
	elgg_register_plugin_hook_handler('search', 'group', 'search_advanced_groups_hook');
	
	// tags and comments are a bit different.
	// register a search types and a hooks for them.
// 	elgg_register_plugin_hook_handler('search_advanced_types', 'get_types', 'search_advanced_custom_types_tags_hook');
//  	elgg_register_plugin_hook_handler('search', 'tags', 'search_advanced_tags_hook');
	
// 	elgg_register_plugin_hook_handler('search_advanced_types', 'get_types', 'search_advanced_custom_types_comments_hook');
	elgg_register_plugin_hook_handler('search', 'comments', 'search_advanced_comments_hook');
}

/**
* Returns a where clause for a search query. 
* 
* Search Advanced: added the ability to use a wildcard in full text search
*
* @param str $table Prefix for table to search on
* @param array $fields Fields to match against
* @param array $params Original search params
* @return str
*/
function search_advanced_get_where_sql($table, $fields, $params, $use_fulltext = TRUE) {
	global $CONFIG;
	$query = sanitise_string($params['query']);

	// add the table prefix to the fields
	if ($table) {
		foreach ($fields as $i => $field) {
			$fields[$i] = "$table.$field";
		}
	}

	$likes = array();
	foreach ($fields as $field) {
		$likes[] = "$field LIKE '%$query%'";
	}
	$likes_str = implode(' OR ', $likes);
	$where = "($likes_str)";

	return $where;
}

function search_advanced_get_keywords(){
	$result = array();
	
	$plugin_entity = elgg_get_plugin_from_id("search_advanced");
	if($plugin_entity){

		// check if cachefile exists, if not create
		$file = new ElggFile();
		$file->owner_guid = $plugin_entity->getGUID();
		$file->setFilename("search_advanced_keywords_cache.json");
		if(!$file->exists()){
			
			// create new cache
			$keywords = array();
			
			// check global tags plugin
			if(elgg_is_active_plugin("global_tags")){
				if($setting = elgg_get_plugin_setting("global_tags", "global_tags")) {
					$tags = string_to_tag_array($setting);
					if(!empty($tags)){
						$keywords = array_merge($keywords, $tags);
					}
				} 
			}
			
			// check categories plugin
			if(elgg_is_active_plugin("categories")){
				
				$categories = elgg_get_site_entity()->categories;
				if(!is_array($categories)){
					$categories = array($categories);
				}
				
				if(!empty($categories)){
					$keywords = array_merge($keywords, $categories);
				}
			}
			// remove duplicates
			$keywords = array_unique($keywords);
			
			// sort naturally
			natcasesort($keywords);
			
			// save as json
			$data = json_encode($keywords);
			$file->open("write");
			$file->write($data);
			$file->close();
		}
		
		// read from cachefile
		if($file->open("read")){
			if($file_contents = $file->grabFile()){
				$result = json_decode($file_contents);
			}	
		}
	}
	return $result;
}

/**
 * 
 * Removes the file cache for keywords
 */
function search_advanced_clear_keywords_cache(){
	$plugin_entity = elgg_get_plugin_from_id("search_advanced");
	if($plugin_entity){
		
		// check if cachefile exists, if exists delete it	
		$file = new ElggFile();
		$file->owner_guid = $plugin_entity->getGUID();
		$file->setFilename("search_advanced_keywords_cache.json");
		if($file->exists()){
			$file->delete();
		}
	}
}
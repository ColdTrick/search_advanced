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
// 	elgg_register_plugin_hook_handler('search_advanced_types', 'get_types', 'search_advanced_custom_types_tags_hook');
 	elgg_register_plugin_hook_handler('search', 'tags', 'search_advanced_tags_hook');
	
// 	elgg_register_plugin_hook_handler('search_advanced_types', 'get_types', 'search_advanced_custom_types_comments_hook');
// 	elgg_register_plugin_hook_handler('search', 'comments', 'search_advanced_comments_hook');
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
	$query = $params['query'];

	// add the table prefix to the fields
	foreach ($fields as $i => $field) {
		if ($table) {
			$fields[$i] = "$table.$field";
		}
	}

	$where = '';

	// if query is shorter than the min for fts words
	// it's likely a single acronym or similar
	// switch to literal mode
	if (true || elgg_strlen($query) < $CONFIG->search_info['min_chars']) {
		$likes = array();
		$query = sanitise_string($query);
		foreach ($fields as $field) {
			$likes[] = "$field LIKE '%$query%'";
		}
		$likes_str = implode(' OR ', $likes);
		$where = "($likes_str)";
	} else {
		// if we're not using full text, rewrite the query for bool mode.
		// exploiting a feature(ish) of bool mode where +-word is the same as -word
		if (!$use_fulltext) {
			$query = '+' . str_replace(' ', ' +', $query);
		}

		// if using advanced, boolean operators, or paired "s, switch into boolean mode
		$booleans_used = preg_match("/([\-\+~])([\w]+)/i", $query);
		$advanced_search = (isset($params['advanced_search']) && $params['advanced_search']);
		$quotes_used = (elgg_substr_count($query, '"') >= 2);

		if (!$use_fulltext || $booleans_used || $advanced_search || $quotes_used) {
			$options = 'IN BOOLEAN MODE';
		} else {
			// natural language mode is default and this keyword isn't supported in < 5.1
			//$options = 'IN NATURAL LANGUAGE MODE';
			$options = '';
		}

		// if short query, use query expansion.
		// @todo doesn't seem to be working well.
		//		if (elgg_strlen($query) < 5) {
		//			$options .= ' WITH QUERY EXPANSION';
		//		}
		$query = sanitise_string($query);

		$fields_str = implode(',', $fields);
		$where = "(MATCH ($fields_str) AGAINST ('$query*' $options))"; // Search Advanced: added the wildcard
	}

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
			$keywords = array_unique($keywords);
			natcasesort($keywords);
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
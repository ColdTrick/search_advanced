<?php
/**
 * Plugin related functions are bundled here
 */

/**
 * Unregisters the default search hook
 * 
 * @return void
 */
function search_advanced_unregister_default_search_hooks() {
	// register some default search hooks
	elgg_unregister_plugin_hook_handler('search', 'object', 'search_objects_hook');
	elgg_unregister_plugin_hook_handler('search', 'user', 'search_users_hook');
	elgg_unregister_plugin_hook_handler('search', 'group', 'search_groups_hook');
	
	// tags are no longer a custom type but integrated with the entity searches
	elgg_unregister_plugin_hook_handler('search_advanced_types', 'get_types', 'search_custom_types_tags_hook');
	elgg_unregister_plugin_hook_handler('search', 'tags', 'search_tags_hook');	
	elgg_unregister_plugin_hook_handler('search_types', 'get_types', 'search_custom_types_tags_hook');
}

/**
 * Registers the new search hooks
 * 
 * @return void
 */
function search_advanced_register_search_hooks() {
	// register some default search hooks
	elgg_register_plugin_hook_handler('search', 'object', 'search_advanced_objects_hook');
	elgg_register_plugin_hook_handler('search', 'user', 'search_advanced_users_hook');
	elgg_register_plugin_hook_handler('search', 'group', 'search_advanced_groups_hook');
}

/**
* Returns a where clause for a search query.
*
* Search Advanced: added the ability to use a wildcard in full text search
*
* @param string  $table        Prefix for table to search on
* @param array   $fields       Fields to match against
* @param array   $params       Original search params
* @param boolean $use_fulltext Toggle the use of full text search
* 
* @return string
*/
function search_advanced_get_where_sql($table, $fields, $params, $use_fulltext = TRUE) {
	$query = elgg_extract("query", $params, "");
	if (empty($query)) {
		return "";
	}
	
	$query_array = explode(" ", $query);
	
	if (count($query_array) > 1) {
		$multi_query = array();
		foreach ($query_array as $value) {
			$temp_field = trim($value);
			if (!empty($temp_field)) {
				$multi_query[] = $temp_field;
			}
		}
		
		if (count($multi_query) > 1) {
			$query = $multi_query;
		}
	}
	
	// add the table prefix to the fields
	if ($table) {
		foreach ($fields as $i => $field) {
			$fields[$i] = "$table.$field";
		}
	}
	
	if (!is_array($query)) {
		$query = array($query);
	}

	$likes = array();
	foreach ($fields as $field) {
		$field_likes = array();
		foreach ($query as $query_part) {
			$query_part = sanitise_string($query_part);
		
			$field_likes[] = "$field LIKE '%$query_part%'";
		}
		$likes[] = "(" . implode(' AND ', $field_likes) . ")";
	}
	$likes_str = implode(' OR ', $likes);
	$where = "($likes_str)";

	return $where;
}

/**
 * Returns keywords that will autocomplete in the searchbox
 * 
 * @return array
 */
function search_advanced_get_keywords() {
	$result = array();
	
	$plugin_entity = elgg_get_plugin_from_id("search_advanced");
	if ($plugin_entity) {

		// check if cachefile exists, if not create
		$file = new ElggFile();
		$file->owner_guid = $plugin_entity->getGUID();
		$file->setFilename("search_advanced_keywords_cache.json");
		if (!$file->exists()) {
			
			// create new cache
			$keywords = array();
			
			// check global tags plugin
			if (elgg_is_active_plugin("global_tags")) {
				if ($setting = elgg_get_plugin_setting("global_tags", "global_tags")) {
					$tags = string_to_tag_array($setting);
					if (!empty($tags)) {
						$keywords = array_merge($keywords, $tags);
					}
				}
			}
			
			// check categories plugin
			if (elgg_is_active_plugin("categories")) {
				
				$categories = elgg_get_site_entity()->categories;
				if (!is_array($categories)) {
					$categories = array($categories);
				}
				
				if (!empty($categories)) {
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
		if ($file->open("read")) {
			if ($file_contents = $file->grabFile()) {
				$result = json_decode($file_contents);
			}
		}
	}
	return $result;
}

/**
 * Removes the file cache for keywords
 * 
 * @return void
 */
function search_advanced_clear_keywords_cache() {
	$plugin_entity = elgg_get_plugin_from_id("search_advanced");
	if ($plugin_entity) {
		// check if cachefile exists, if exists delete it
		$file = new ElggFile();
		$file->owner_guid = $plugin_entity->getGUID();
		$file->setFilename("search_advanced_keywords_cache.json");
		if ($file->exists()) {
			$file->delete();
		}
	}
}

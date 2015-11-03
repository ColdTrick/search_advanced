<?php
/**
 * All hooks are bundled here
 */

/**
 * Adjust the custom types used in search
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_custom_types_hook($hook, $type, $value, $params) {

}

/**
 * Return default results for searches on objects.
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_objects_hook($hook, $type, $value, $params) {
	if (!empty($value)) {
		return;
	}
	
	if (elgg_get_plugin_setting('search_hooks_enabled', 'search_advanced', 'yes') == 'no') {
		return;
	}

	static $tag_name_ids;
	static $tag_value_ids;
	static $valid_tag_names;
	$return_only_count = elgg_extract('count', $params, false);
	
	$query_parts = (array) search_advanced_tag_query_to_array($params['query']);
	if (empty($params['query']) || empty($query_parts)) {
		return ['entities' => [], 'count' => 0];
	}
	
	if (!isset($tag_name_ids)) {
		$tag_name_ids = false;
		if ($valid_tag_names = elgg_get_registered_tag_metadata_names()) {
			$tag_name_ids = search_advanced_get_metastring_ids($valid_tag_names);
		}
	}
	
	$params['joins'] = elgg_extract('joins', $params, []);
	
	$db_prefix = elgg_get_config('dbprefix');
	
	$params["joins"][] = "JOIN {$db_prefix}objects_entity oe ON e.guid = oe.guid";
	
	$fields = ['title', 'description'];
	$where = search_advanced_get_where_sql('oe', $fields, $params, false);

	$params["wheres"] = elgg_extract("wheres", $params, []);
	
	if ($tag_name_ids) {
		// look up value ids to save a join
		if (!isset($tag_value_ids)) {
			$tag_value_ids = search_advanced_get_metastring_ids($query_parts);
		}
		
		if (empty($tag_value_ids)) {
			$params['wheres'][] = $where;
		} else {
			$params["joins"][] = "LEFT OUTER JOIN {$db_prefix}metadata md on e.guid = md.entity_guid";
			
			$md_where = "((md.name_id IN (" . implode(",", $tag_name_ids) . ")) AND md.value_id IN (" . implode(",", $tag_value_ids) . "))";
			$params['wheres'][] = "(($where) OR ($md_where))";
		}
	} else {
		$params['wheres'][] = $where;
	}
	
	$params['count'] = true;
	$count = elgg_get_entities($params);

	// no need to continue if nothing here.
	if (!$count || $return_only_count) {
		return ['entities' => [], 'count' => $count];
	}
	
	$params['count'] = false;
	$entities = elgg_get_entities($params);

	// add the volatile data for why these entities have been returned.
	foreach ($entities as $entity) {
		$title = search_get_highlighted_relevant_substrings($entity->title, $params['query']);
		$entity->setVolatileData('search_matched_title', $title);

		$desc = search_get_highlighted_relevant_substrings($entity->description, $params['query']);
		$entity->setVolatileData('search_matched_description', $desc);
		
		if (empty($valid_tag_names)) {
			continue;
		}
		
		$matched_tags_strs = [];
		
		// get tags for each tag name requested to find which ones matched.
		foreach ($valid_tag_names as $tag_name) {
			$tags = $entity->getTags($tag_name);
		
			// @todo make one long tag string and run this through the highlight
			// function.  This might be confusing as it could chop off
			// the tag labels.
		
			foreach ($query_parts as $part) {
				if (in_array(strtolower($part), array_map('strtolower', $tags))) {
					if (is_array($tags)) {
						$tag_name_str = elgg_echo("tag_names:$tag_name");
						$matched_tags_strs[] = "$tag_name_str: " . implode(', ', $tags);
						// only need it once for each tag
						break;
					}
				}
			}
		}
			
		$tags_str = implode('. ', $matched_tags_strs);
		$tags_str = search_get_highlighted_relevant_substrings($tags_str, $params['query']);
		
		$entity->setVolatileData('search_matched_extra', $tags_str);
	}

	return [
		'entities' => $entities,
		'count' => $count,
	];
}

/**
 * Return default results for searches on everything
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_combined_all_hook($hook, $type, $value, $params) {
	if (!empty($value)) {
		return;
	}
	
	if (elgg_get_plugin_setting('search_hooks_enabled', 'search_advanced', 'yes') == 'no') {
		return;
	}

	static $tag_name_ids;
	static $tag_value_ids;
	static $valid_tag_names;
	$return_only_count = elgg_extract('count', $params, false);
	
	$query_parts = (array) search_advanced_tag_query_to_array($params['query']);
	if (empty($params['query']) || empty($query_parts)) {
		return ['entities' => [], 'count' => 0];
	}
	
	if (!isset($tag_name_ids)) {
		$tag_name_ids = false;
		if ($valid_tag_names = elgg_get_registered_tag_metadata_names()) {
			$tag_name_ids = search_advanced_get_metastring_ids($valid_tag_names);
		}
	}
	
	$params['joins'] = elgg_extract('joins', $params, []);
	
	$db_prefix = elgg_get_config('dbprefix');
	
	$types = elgg_extract('type_subtype_pairs', $params);
	
	$wheres = [];
	foreach ($types as $entity_type => $entity_subtypes) {
		switch($entity_type) {
			case 'object':
				$params["joins"][] = "LEFT OUTER JOIN {$db_prefix}objects_entity oe ON e.guid = oe.guid";
				
				$fields = ['title', 'description'];
				$wheres[] = search_advanced_get_where_sql('oe', $fields, $params, false);
				break;
			case 'user':
				$params["joins"][] = "LEFT OUTER JOIN {$db_prefix}users_entity ue ON e.guid = ue.guid";
				
				$fields = ['username', 'name'];
				$wheres[] = search_advanced_get_where_sql('ue', $fields, $params, false);
				break;
			case 'group':
				$params["joins"][] = "LEFT OUTER JOIN {$db_prefix}groups_entity ge ON e.guid = ge.guid";
				
				$fields = ['name', 'description'];
				$wheres[] = search_advanced_get_where_sql('ge', $fields, $params, false);
				break;
		}
	}
	
	$where = '(' . implode(' OR ', $wheres) . ')';
		
	$params["wheres"] = elgg_extract("wheres", $params, []);
	
	if ($tag_name_ids) {
		// look up value ids to save a join
		if (!isset($tag_value_ids)) {
			$tag_value_ids = search_advanced_get_metastring_ids($query_parts);
		}
		
		if (empty($tag_value_ids)) {
			$params['wheres'][] = $where;
		} else {
			$params["joins"][] = "LEFT OUTER JOIN {$db_prefix}metadata md on e.guid = md.entity_guid";
			
			$md_where = "((md.name_id IN (" . implode(",", $tag_name_ids) . ")) AND md.value_id IN (" . implode(",", $tag_value_ids) . "))";
			$params['wheres'][] = "(($where) OR ($md_where))";
		}
	} else {
		$params['wheres'][] = $where;
	}
	
	$params['count'] = true;
	$count = elgg_get_entities($params);
	
	// no need to continue if nothing here.
	if (!$count || $return_only_count) {
		return ['entities' => [], 'count' => $count];
	}
		
	$params['count'] = false;
	$entities = elgg_get_entities($params);

	// add the volatile data for why these entities have been returned.
	foreach ($entities as $entity) {
		$title = search_get_highlighted_relevant_substrings($entity->title, $params['query']);
		$entity->setVolatileData('search_matched_title', $title);

		$desc = search_get_highlighted_relevant_substrings($entity->description, $params['query']);
		$entity->setVolatileData('search_matched_description', $desc);
		
		if (empty($valid_tag_names)) {
			continue;
		}
		
		$matched_tags_strs = [];
		
		// get tags for each tag name requested to find which ones matched.
		foreach ($valid_tag_names as $tag_name) {
			$tags = $entity->getTags($tag_name);
		
			// @todo make one long tag string and run this through the highlight
			// function.  This might be confusing as it could chop off
			// the tag labels.
		
			foreach ($query_parts as $part) {
				if (in_array(strtolower($part), array_map('strtolower', $tags))) {
					if (is_array($tags)) {
						$tag_name_str = elgg_echo("tag_names:$tag_name");
						$matched_tags_strs[] = "$tag_name_str: " . implode(', ', $tags);
						// only need it once for each tag
						break;
					}
				}
			}
		}
			
		$tags_str = implode('. ', $matched_tags_strs);
		$tags_str = search_get_highlighted_relevant_substrings($tags_str, $params['query']);
		
		$entity->setVolatileData('search_matched_extra', $tags_str);
	}

	return [
		'entities' => $entities,
		'count' => $count,
	];
}

/**
 * Return default results for searches on groups.
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_groups_hook($hook, $type, $value, $params) {
	if (!empty($value)) {
		return;
	}
	
	if (elgg_get_plugin_setting('search_hooks_enabled', 'search_advanced', 'yes') == 'no') {
		return;
	}
	
	$return_only_count = elgg_extract('count', $params, false);
	
	$query_parts = (array) search_advanced_tag_query_to_array($params['query']);
	if (empty($params['query']) || empty($query_parts)) {
		return ['entities' => [], 'count' => 0];
	}
	
	$fields = ['name', 'description'];
	
	$where = search_advanced_get_where_sql('ge', $fields, $params, FALSE);
	
	$db_prefix = elgg_get_config('dbprefix');
	
	$params['joins'] = ["JOIN {$db_prefix}groups_entity ge ON e.guid = ge.guid"];
	
	$profile_fields = array_keys(elgg_get_config('group'));
	if (!empty($profile_fields)) {
		$params['joins'] = [
			"JOIN {$db_prefix}groups_entity ge ON e.guid = ge.guid",
			"JOIN {$db_prefix}metadata md on e.guid = md.entity_guid",
			"JOIN {$db_prefix}metastrings msv ON md.value_id = msv.id"
		];

		$profile_field_metadata_search_values = elgg_get_plugin_setting("group_profile_fields_metadata_search", "search_advanced", array());
		if (!empty($profile_field_metadata_search_values)) {
			$profile_field_metadata_search_values = json_decode($profile_field_metadata_search_values, true);
		}
			
		foreach ($profile_fields as $key => $field) {
			if (in_array($field, $profile_field_metadata_search_values)) {
				unset($profile_fields[$key]);
			}
		}
		
		$tag_name_ids = search_advanced_get_metastring_ids($profile_fields);

		if ($tag_name_ids) {
			$likes = [];
			foreach ($query_parts as $query_value) {
				$query_value = sanitise_string($query_value);
				if (!empty($query_value)) {
					$likes[] = "msv.string LIKE '%$query_value%'";
				}
			}
							
			$md_where = "((md.name_id IN (" . implode(",", $tag_name_ids) . ")) AND (" . implode(" OR ", $likes) . "))";
			$params['wheres'] = array("(($where) OR ($md_where))");
		} else {
			$params['wheres'] = array($where);
		}
	} else {
		$params['wheres'] = array($where);
	}
	
	// override subtype -- All groups should be returned regardless of subtype.
	$params['subtype'] = ELGG_ENTITIES_ANY_VALUE;

	$params['count'] = TRUE;
	$count = elgg_get_entities($params);
	
	// no need to continue if nothing here.
	if (!$count || $return_only_count) {
		return ['entities' => [], 'count' => $count];
	}
	
	$params['count'] = FALSE;
	$entities = elgg_get_entities($params);

	$query = sanitise_string($params['query']);
	
	// add the volatile data for why these entities have been returned.
	foreach ($entities as $entity) {
		$name = search_get_highlighted_relevant_substrings($entity->name, $query);
		$entity->setVolatileData('search_matched_title', $name);

		$description = search_get_highlighted_relevant_substrings($entity->description, $query);
		$entity->setVolatileData('search_matched_description', $description);
	}

	return [
		'entities' => $entities,
		'count' => $count,
	];
}

/**
 * Return default results for searches on users.
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_users_hook($hook, $type, $value, $params) {
	if (!empty($value)) {
		return;
	}
	
	if (elgg_get_plugin_setting('search_hooks_enabled', 'search_advanced', 'yes') == 'no') {
		return;
	}
	
	$return_only_count = elgg_extract('count', $params, false);
	
	$db_prefix = elgg_get_config('dbprefix');
	$params['joins'] = [
		"JOIN {$db_prefix}users_entity ue ON e.guid = ue.guid",
	];
	
	if (isset($params["container_guid"])) {
		$container_entity = get_entity($params["container_guid"]);
	}
	
	if (isset($container_entity) && $container_entity instanceof ElggGroup) {
		// check for group membership relation
		$params["relationship"] = "member";
		$params["relationship_guid"] = $params["container_guid"];
		$params["inverse_relationship"] = false;
		
		unset($params["container_guid"]);
	} else {
		// check for site relation ship
		$params["relationship"] = "member_of_site";
		$params["relationship_guid"] = elgg_get_site_entity()->getGUID();
		$params["inverse_relationship"] = true;
	}
	
	$i = 0;
	
	if (!empty($params["query"])) {
		$query_parts = (array) search_advanced_tag_query_to_array($params['query']);
		if (empty($query_parts)) {
			return ['entities' => [], 'count' => 0];
		}
		
		$fields = array('username', 'name');
		$where = search_advanced_get_where_sql('ue', $fields, $params, FALSE);
		
		// profile fields
		$profile_fields = array_keys(elgg_get_config('profile_fields'));
		if (!empty($profile_fields)) {
			
			$profile_field_metadata_search_values = elgg_get_plugin_setting("user_profile_fields_metadata_search", "search_advanced", array());
			if (!empty($profile_field_metadata_search_values)) {
				$profile_field_metadata_search_values = json_decode($profile_field_metadata_search_values, true);
			}
			
			foreach ($profile_fields as $key => $field) {
				if (in_array($field, $profile_field_metadata_search_values)) {
					unset($profile_fields[$key]);
				}
			}
			
			$tag_name_ids = search_advanced_get_metastring_ids($profile_fields);
					
			if (!empty($tag_name_ids)) {
				$likes = [];
				
				foreach ($query_parts as $query_value) {
					$query_value = sanitise_string($query_value);
					if (!empty($query_value)) {
						$likes[] = "msv$i.string LIKE '%$query_value%'";
					}
				}

				$params["joins"][] = "JOIN {$db_prefix}metadata md$i on e.guid = md$i.entity_guid";
				$params["joins"][] = "JOIN {$db_prefix}metastrings msv$i ON md$i.value_id = msv$i.id";
				
				$md_where = "((md$i.name_id IN (" . implode(",", $tag_name_ids) . ")) AND (" . implode(" OR ", $likes) . "))";
					
				$where = "(($where) OR ($md_where))";
				
				$i++;
			}
		}
		
		$params['wheres'] = array($where);
	}
	
	// additional filters
	$search_filter = elgg_extract('search_filter', $params);
	if (!empty($search_filter)) {
		// on profile fields?
		$profile_fields = elgg_extract('profile_fields', $search_filter);
		if (!empty($profile_fields)) {
			$profile_field_likes = [];
			
			// use soundex on some fields
			$profile_soundex = elgg_extract('profile_fields_soundex', $search_filter);
			
			foreach ($profile_fields as $field_name => $field_value) {
				$field_value = trim(sanitise_string($field_value));
				if (!empty($field_value)) {
					$tag_name_id = elgg_get_metastring_id($field_name);
					
					$params["joins"][] = "JOIN {$db_prefix}metadata md$i on e.guid = md$i.entity_guid";
					$params["joins"][] = "JOIN {$db_prefix}metastrings msv$i ON md$i.value_id = msv$i.id";
					
					// do a soundex match
					if (is_array($profile_soundex) && in_array($field_name, $profile_soundex)) {
						$profile_field_likes[] = "md$i.name_id = $tag_name_id AND soundex(CONCAT('X', msv$i.string)) = soundex(CONCAT('X','$field_value'))";
					} else {
						$profile_field_likes[] = "md$i.name_id = $tag_name_id AND msv$i.string LIKE '%$field_value%'";
					}
					
					$i++;
				}
			}
			
			if (!empty($profile_field_likes)) {
				$profile_field_where = "(" . implode(" AND ", $profile_field_likes) . ")";
				
				if (empty($params["wheres"])) {
					$params["wheres"] = array($profile_field_where);
				} else {
					$params["wheres"] = array($params["wheres"][0] . " AND " . $profile_field_where);
				}
			}
		}
	}
	
	if (empty($params['wheres'])) {
		return ['entities' => [], 'count' => 0];
	}
	
	$wheres = (array) elgg_extract("wheres", $params);
	$wheres[] = "ue.banned = 'no'";
	$params["wheres"] = $wheres;
	
	// override subtype -- All users should be returned regardless of subtype.
	$params['subtype'] = ELGG_ENTITIES_ANY_VALUE;

	$params['count'] = true;
	$count = elgg_get_entities_from_relationship($params);

	// no need to continue if nothing here.
	if (!$count || $return_only_count) {
		return ['entities' => [], 'count' => $count];
	}
	
	$params['count'] = false;
	$entities = elgg_get_entities_from_relationship($params);

	$query = sanitise_string($params['query']);
	
	// add the volatile data for why these entities have been returned.
	foreach ($entities as $entity) {
		$name = search_get_highlighted_relevant_substrings($entity->name, $query);
		$entity->setVolatileData('search_matched_title', $name);
		
		$username = search_get_highlighted_relevant_substrings($entity->username, $query);
		$entity->setVolatileData('search_matched_description', $username);
	}

	return [
		'entities' => $entities,
		'count' => $count,
	];
}

/**
 * Return the data from the default search hook
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_fallback_search_hook($hook, $type, $value, $params) {
	if (!empty($value)) {
		return;
	}
	
	if (!in_array($type, ['object', 'user', 'group', 'tags'])) {
		return;
	}
	
	switch ($type) {
		case 'object':
			return search_objects_hook($hook, $type, $value, $params);
		case 'user':
			return search_users_hook($hook, $type, $value, $params);
		case 'group':
			return search_groups_hook($hook, $type, $value, $params);
		case 'tags':
			return search_tags_hook($hook, $type, $value, $params);
	}
}

/**
 * Registers menu type selection menu items
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_register_menu_type_selection($hook, $type, $value, $params) {
	$result = $value;
	
	$types = get_registered_entity_types();
	$custom_types = elgg_trigger_plugin_hook("search_types", "get_types", array(), array());
	
	$result[] = ElggMenuItem::factory(array(
		"name" => "all",
		"text" => "<a>" . elgg_echo("all") . "</a>",
		"href" => false
	));
	$result[] = ElggMenuItem::factory(array(
		"name" => "item:user",
		"text" => "<a rel='user'>" . elgg_echo("item:user") . "</a>",
		"href" => false
	));
	$result[] = ElggMenuItem::factory(array(
		"name" => "item:group",
		"text" => "<a rel='group'>" . elgg_echo("item:group") . "</a>",
		"href" => false
	));
	
	foreach ($types["object"] as $subtype) {
		$result[] = ElggMenuItem::factory(array(
			"name" => "item:object:$subtype",
			"text" => "<a rel='object " . $subtype . "'>" . elgg_echo("item:object:" . $subtype) . "</a>",
			"href" => false,
			"title" => elgg_echo("item:object:$subtype")
		));
	}
	
	foreach ($custom_types as $type) {
		$result[] = ElggMenuItem::factory(array(
			"name" => "search_types:$type",
			"text" => "<a rel='" . $type . "'>" . elgg_echo("search_types:$type") . "</a>",
			"href" => false,
			"title" => elgg_echo("search_types:$type")
		));
	}
	
	return $result;
}

/**
 * Registers menu items related to search results listing
 *
 * @param string       $hook   name of hook
 * @param string       $type   type of hook
 * @param unknown_type $value  current value
 * @param array        $params parameters
 *
 * @return array
 */
function search_advanced_register_menu_list($hook, $type, $value, $params) {
	$result = $value;
	
	$url = search_advanced_get_search_url();
	$current_list_type = search_advanced_get_list_type();
	$title = elgg_echo('search_advanced:menu:search_list:list:title');
	
	$result[] = ElggMenuItem::factory([
		'name' => 'list',
		'text' => elgg_view_icon('list'),
		'href' => '#',
		'title' => $title,
		'priority' => 999
	]);

	$result[] = ElggMenuItem::factory([
		'name' => 'list_entity',
		'text' => elgg_echo('search_advanced:menu:search_list:entity'),
		'href' => elgg_http_add_url_query_elements($url, ['list_type' => 'entity']),
		'parent_name' => 'list',
		'selected' => ($current_list_type === 'entity'),
		'title' => $title
	]);

	$result[] = ElggMenuItem::factory([
		'name' => 'list_compact',
		'text' => elgg_echo('search_advanced:menu:search_list:compact'),
		'href' => elgg_http_add_url_query_elements($url, ['list_type' => 'compact']),
		'parent_name' => 'list',
		'selected' => ($current_list_type === 'compact'),
		'title' => $title
	]);
	
	return $result;
}

/**
 * Search in both page and page_top entities
 *
 * @param string $hook   the name of the hook
 * @param string $type   the type of the hook
 * @param mixed  $value  the current return value
 * @param array  $params supplied params
 */
function search_advanced_search_page($hook, $type, $value, $params) {

	if (empty($params) || !is_array($params)) {
		return $value;
	}

	$subtype = elgg_extract("subtype", $params);
	if (empty($subtype) || ($subtype !== "page")) {
		return $value;
	}

	unset($params["subtype"]);
	$params["subtypes"] = ["page", "page_top"];

	// trigger the 'normal' object search as it can handle the added options
	return elgg_trigger_plugin_hook('search', 'object', $params, []);
}

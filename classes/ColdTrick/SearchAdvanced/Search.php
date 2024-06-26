<?php

namespace ColdTrick\SearchAdvanced;

/**
 * Various search callbacks
 */
class Search {
	
	const QUERY_PLACEHOLDER = '_search_advanced_empty_query_placeholder';
	
	/**
	 * Allow searches with empty queries when searching profile fields
	 *
	 * @param \Elgg\Event $event 'search:params', 'user'
	 *
	 * @return void|array
	 */
	public static function allowEmptyQueryWithProfileSearch(\Elgg\Event $event) {
		
		$search_params = $event->getValue();
		
		$allow_empty_query = (bool) elgg_extract('allow_empty_query', $search_params, false);
		$filter = (array) elgg_extract('search_filter', $search_params, []);
		if (empty($filter) || $allow_empty_query) {
			return;
		}
		
		$profile_fields = (array) elgg_extract('profile_fields', $filter, []);
		if (empty($profile_fields)) {
			return;
		}
		
		foreach ($profile_fields as $query) {
			if (elgg_is_empty($query)) {
				continue;
			}
			
			$allow_empty_query = true;
			break;
		}
		
		if ($allow_empty_query !== true) {
			return;
		}
		
		$search_params['allow_empty_query'] = true;
		
		return $search_params;
	}
	
	/**
	 * Allow searches with empty queries
	 *
	 * @param \Elgg\Event $event 'search:params', 'all'
	 *
	 * @return void|array
	 */
	public static function allowEmptyQuery(\Elgg\Event $event) {
		
		$search_params = $event->getValue();
		
		$allow_empty_query = (bool) elgg_extract('allow_empty_query', $search_params, false);
		if (!$allow_empty_query && elgg_get_plugin_setting('query_required', 'search_advanced') !== 'no') {
			return;
		}
		
		$query = elgg_extract('query', $search_params);
		if (!elgg_is_empty($query)) {
			return;
		}
		
		// set dummy search query
		$search_params['query'] = self::QUERY_PLACEHOLDER;
		
		// register event to unset the dummy query
		$entity_type = elgg_extract('type', $search_params, 'all', false);
		
		elgg_register_event_handler('search:options', $entity_type, __NAMESPACE__ . '\Search::unsetEmptyQueryPlaceholder', 1);
		
		return $search_params;
	}
	
	/**
	 * Unset empty query placeholder
	 *
	 * @param \Elgg\Event $event 'search:options', '*'
	 *
	 * @return void|array
	 */
	public static function unsetEmptyQueryPlaceholder(\Elgg\Event $event) {
		
		$result = $event->getValue();
		$query = elgg_extract('query', $result);
		if ($query !== self::QUERY_PLACEHOLDER) {
			return;
		}
		
		unset($result['query']);
		unset($result['query_parts']);
		unset($result['wheres']['search']);
		
		return $result;
	}
		
	/**
	 * Gets search types
	 *
	 * @param \Elgg\Event $event 'search:config', 'search_types'
	 *
	 * @return array
	 */
	public static function getSearchTypes(\Elgg\Event $event) {
		
		$result = $event->getValue();
		
		$combine_results = elgg_get_plugin_setting('combine_search_results', 'search_advanced');
		switch ($combine_results) {
			case 'objects':
				$result[] = 'combined:objects';
				break;
			case 'all':
				$result[] = 'combined:all';
				break;
		}
		
		return $result;
	}

	/**
	 * Returns the search results for users used in the autocomplete
	 *
	 * @param \Elgg\Event $event 'elgg.data', 'page'
	 *
	 * @return array
	 */
	public static function getAutocompleteHelpers(\Elgg\Event $event) {
		$return = $event->getValue();
		$return['search_advanced']['helpers'] = [];

		if (elgg_get_plugin_setting('enable_autocomplete_helpers', 'search_advanced') === 'no') {
			return $return;
		}
				
		$result = [];
		
		$query = '___PLACEHOLDER___';
		
		$route = _elgg_services()->request->getRoute();
		$route_name = '';
		if ($route) {
			$entity_guid = (int) elgg_extract('guid', $route->getMatchedParameters());
			if ($entity_guid) {
				$entity = get_entity($entity_guid);
			}
			
			$route_name = $route->getName();
		}
		
		$owner = null;
		$container = null;
		$type = null;
		$subtype = null;
		
		$route_parts = explode(':', $route_name);
		
		if ((elgg_extract(0, $route_parts) !== 'collection') && isset($entity) && $entity instanceof \ElggEntity) {
			$type = $entity->getType();
			$subtype = $entity->getSubtype();
			
			$owner = $entity->getOwnerEntity();
			$container = $entity->getContainerEntity();
			
			if ($entity instanceof \ElggGroup) {
				$container = $entity;
			}
		} else {
			$page_owner = elgg_get_page_owner_entity();
			if ($page_owner instanceof \ElggGroup) {
				$container = $page_owner;
			} elseif ($page_owner instanceof \ElggUser) {
				$owner = $page_owner;
			}
		
			if (in_array(elgg_extract(0, $route_parts), ['default', 'collection'])) {
				$type = elgg_extract(1, $route_parts);
				$subtype = elgg_extract(2, $route_parts);
			}
		}
		
		// validate searchable types
		if ($type && $subtype) {
			$searchable_subtypes = elgg_extract($type, elgg_entity_types_with_capability('searchable'), []);
			if (in_array($subtype, $searchable_subtypes)) {
				$result[] = [
					'type' => 'placeholder',
					'content' => self::formatAutocompletePlaceholder([
						'icon' => 'search',
						'text' => $query,
						'info' => elgg_echo('search_advanced:autocomplete:placeholder:type', [elgg_echo("item:{$type}:{$subtype}")]),
					]),
					'href' => elgg_generate_url('default:search', [
						'q' => $query,
						'entity_type' => $type,
						'entity_subtype' => $subtype,
						'search_type' => 'entities',
					]),
				];
			}
		}
		
		if ($owner instanceof \ElggUser) {
			$result[] = [
				'type' => 'placeholder',
				'content' => self::formatAutocompletePlaceholder([
					'icon' => 'search',
					'text' => $query,
					'info' => elgg_echo('search_advanced:autocomplete:placeholder:owner', [$owner->getDisplayName()]),
				]),
				'href' => elgg_generate_url('default:search', [
					'q' => $query,
					'owner_guid' => $owner->guid,
				]),
			];
		}
		
		if ($container instanceof \ElggGroup) {
			$result[] = [
				'type' => 'placeholder',
				'content' => self::formatAutocompletePlaceholder([
					'icon' => 'search',
					'text' => $query,
					'info' => elgg_echo('search_advanced:autocomplete:placeholder:container', [$container->getDisplayName()]),
				]),
				'href' => elgg_generate_url('default:search', [
					'q' => $query,
					'container_guid' => $container->guid,
				]),
			];
		}
		
		if (empty($result)) {
			return $return;
		}
		
		$result[] = [
			'type' => 'placeholder',
			'content' => self::formatAutocompletePlaceholder([
				'icon' => 'search',
				'text' => $query,
				'info' => elgg_echo('search_advanced:autocomplete:placeholder:all'),
			]),
			'href' => elgg_generate_url('default:search', [
				'q' => $query,
			]),
		];
		
		$return['search_advanced']['helpers'] = $result;
		
		return $return;
	}

	/**
	 * Returns the search results for users used in the autocomplete
	 *
	 * @param \Elgg\Event $event 'autocomplete', 'search_advanced'
	 *
	 * @return array
	 */
	public static function getAutocompleteUsers(\Elgg\Event $event) {
		
		if (elgg_get_plugin_setting('enable_autocomplete_content', 'search_advanced') === 'no') {
			return;
		}
		
		$user_options = [
			'query' => $event->getParam('query'),
			'limit' => $event->getParam('limit'),
			'type' => 'user',
			'subtype' => 'user',
		];
		
		$users = elgg_search($user_options);
		if (empty($users)) {
			return;
		}
		
		$users_count = count($users);
		if ($users_count >= elgg_extract('limit', $user_options)) {
			$user_options['count'] = true;
			$users_count = elgg_search($user_options);
		}
		
		$result = $event->getValue();
		
		$result[] = [
			'type' => 'placeholder',
			'content' => self::formatAutocompletePlaceholder([
				'text' => elgg_echo('item:user'),
				'count' => $users_count,
			]),
			'href' => elgg_generate_url('default:search', [
				'entity_type' => 'user',
				'entity_subtype' => 'user',
				'search_type' => 'entities',
				'q' => $event->getParam('query'),
			]),
		];
		
		foreach ($users as $user) {
			$result[] = [
				'type' => 'user',
				'value' => $user->getDisplayName(),
				'href' => $user->getURL(),
				'content' => elgg_view('input/autocomplete/item', [
					'entity' => $user,
					'input_name' => 'search_user',
				])
			];
		}
		
		return $result;
	}

	/**
	 * Returns the search results for groups used in the autocomplete
	 *
	 * @param \Elgg\Event $event 'autocomplete', 'search_advanced'
	 *
	 * @return array
	 */
	public static function getAutocompleteGroups(\Elgg\Event $event) {
		
		if (!elgg_is_active_plugin('groups')) {
			return;
		}
	
		if (elgg_get_plugin_setting('enable_autocomplete_content', 'search_advanced') === 'no') {
			return;
		}
		
		$group_options = [
			'query' => $event->getParam('query'),
			'limit' => $event->getParam('limit'),
			'type' => 'group',
			'subtype' => 'group',
		];
		
		$groups = elgg_search($group_options);
		if (empty($groups)) {
			return;
		}
		
		$groups_count = count($groups);
		if ($groups_count >= elgg_extract('limit', $group_options)) {
			$group_options['count'] = true;
			$groups_count = elgg_search($group_options);
		}
		
		$result = $event->getValue();
		
		$result[] = [
			'type' => 'placeholder',
			'content' => self::formatAutocompletePlaceholder([
				'text' => elgg_echo('item:group'),
				'count' => $groups_count,
			]),
			'href' => elgg_generate_url('default:search', [
				'entity_type' => 'group',
				'entity_subtype' => 'group',
				'search_type' => 'entities',
				'q' => $event->getParam('query'),
			]),
		];
		
		foreach ($groups as $group) {
			$result[] = [
				'type' => 'group',
				'value' => $group->getDisplayName(),
				'href' => $group->getURL(),
				'content' => elgg_view('input/autocomplete/item', [
					'entity' => $group,
					'input_name' => 'search_group',
				]),
			];
		}
		
		return $result;
	}
	
	/**
	 * Formats some information into contents for an autocomplete placeholder
	 *
	 * @param array $params parameters
	 *
	 * @return string
	 */
	protected static function formatAutocompletePlaceholder(array $params) {
		
		$icon = elgg_extract('icon', $params);
		$text = elgg_extract('text', $params);
		$count = elgg_extract('count', $params);
		$info = elgg_extract('info', $params);
		
		if ($icon) {
			$text = elgg_view_icon($icon) . $text;
		}
		
		if ($count) {
			$text .= " ({$count})";
		}
		
		if ($info) {
			$content = "<div>{$text}</div><div>{$info}</div>";
		} else {
			$content = $text;
		}

		return elgg_format_element('label', [], $content);
	}
	
	/**
	 * Remove configured fields from allowed user profile fields
	 *
	 * @param \Elgg\Event $event 'search:fields', 'user'
	 *
	 * @return void|array
	 */
	public static function cleanupUserMetadataFields(\Elgg\Event $event) {
		
		$remove_fields = elgg_get_plugin_setting('user_profile_fields_metadata_search', 'search_advanced');
		if (empty($remove_fields)) {
			return;
		}
		
		$fields = $event->getValue();
		if (empty($fields)) {
			return;
		}
		
		$remove_fields = json_decode($remove_fields, true);
		
		foreach (['metadata', 'annotations'] as $section) {
			if (empty($fields[$section])) {
				continue;
			}
						
			foreach ($fields[$section] as $index => $field_name) {
				if ($section === 'annotations') {
					if (strpos($field_name, 'profile:') === 0) {
						$field_name = substr($field_name, strlen('profile:'));
					}
				}
				
				if (!in_array($field_name, $remove_fields)) {
					continue;
				}
				
				unset($fields[$section][$index]);
			}
		}
		
		return $fields;
	}
	
	/**
	 * Remove configured metadata fields from allowed group metadata fields
	 *
	 * @param \Elgg\Event $event 'search:fields', 'group'
	 *
	 * @return void|array
	 */
	public static function cleanupGroupMetadataFields(\Elgg\Event $event) {
		
		$remove_fields = elgg_get_plugin_setting('group_profile_fields_metadata_search', 'search_advanced');
		if (empty($remove_fields)) {
			return;
		}
		
		$fields = $event->getValue();
		if (empty($fields) || empty($fields['metadata'])) {
			return;
		}
		
		$remove_fields = json_decode($remove_fields, true);
		
		foreach ($fields['metadata'] as $index => $metadata_name) {
			if (!in_array($metadata_name, $remove_fields)) {
				continue;
			}
			
			unset($fields['metadata'][$index]);
		}
		
		return $fields;
	}
	
	/**
	 * Sanitize the profile fields filter to only allow configured profile fields
	 *
	 * @param \Elgg\Event $event 'search:options', 'user'
	 *
	 * @return void|array
	 */
	public static function sanitizeProfileFieldFilter(\Elgg\Event $event) {
		
		$search_params = $event->getValue();
		
		$filter = elgg_extract('search_filter', $search_params);
		if (empty($filter)) {
			return;
		}
		
		$filter_fields = elgg_extract('profile_fields', $filter);
		if (empty($filter_fields)) {
			return;
		}
		
		$profile_fields = [];
		foreach (elgg()->fields->get('user', 'user') as $field) {
			$profile_fields[] = elgg_extract('name', $field);
		}
		
		$configured_fields = elgg_get_plugin_setting('user_profile_fields_search_form', 'search_advanced');
		
		if (empty($profile_fields) || empty($configured_fields)) {
			unset($filter['profile_fields']);
			
			$search_params['search_filter'] = $filter;
			return $search_params;
		}
		
		$configured_fields = json_decode($configured_fields, true);
		
		foreach ($filter_fields as $name => $value) {
			if (!in_array($name, $profile_fields)) {
				// not a profile field
				unset($filter_fields[$name]);
				continue;
			}
			
			if (!in_array($name, $configured_fields)) {
				// not configured as allowed field
				unset($filter_fields[$name]);
				continue;
			}
			
			if (elgg_is_empty($value)) {
				// no submitted value
				unset($filter_fields[$name]);
				continue;
			}
		}
		
		if (empty($filter_fields)) {
			// nothing left
			unset($filter['profile_fields']);
			
			$search_params['search_filter'] = $filter;
			return $search_params;
		}
		
		$filter['profile_fields'] = $filter_fields;
		
		$search_params['search_filter'] = $filter;
		
		return $search_params;
	}
	
	/**
	 * Apply profile search filter on user search
	 *
	 * @param \Elgg\Event $event 'search:options', 'user'
	 *
	 * @return void|array
	 */
	public static function searchUserProfileFilter(\Elgg\Event $event) {
		
		$search_params = $event->getValue();
		
		$filter = elgg_extract('search_filter', $search_params);
		if (empty($filter)) {
			return;
		}
		
		$profile_fields = elgg_extract('profile_fields', $filter);
		if (empty($profile_fields)) {
			return;
		}
		
		foreach ($profile_fields as $profile_field => $value) {
			if (elgg_is_empty($value)) {
				continue;
			}
			
			if (!isset($search_params['annotation_name_value_pairs'])) {
				$search_params['annotation_name_value_pairs'] = [];
			}
			
			$search_params['annotation_name_value_pairs'][] = [
				'name' => "profile:{$profile_field}",
				'value' => "%{$value}%",
				'operand' => 'LIKE',
				'type' => ELGG_VALUE_STRING,
			];
		}
		
		return $search_params;
	}
}

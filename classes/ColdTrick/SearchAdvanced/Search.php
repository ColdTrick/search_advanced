<?php

namespace ColdTrick\SearchAdvanced;

class Search {
	
	/**
	 * Updates params
	 *
	 * @param \Elgg\Hook $hook 'search:params', 'all'
	 *
	 * @return array
	 */
	public static function getParams(\Elgg\Hook $hook) {
		
	}
	
	/**
	 * Gets type/subtype pairs
	 *
	 * @param \Elgg\Hook $hook 'search:config', 'type_subtype_pairs'
	 *
	 * @return array
	 */
	public static function getTypeSubtypePairs(\Elgg\Hook $hook) {
		
	}
	
	/**
	 * Gets search types
	 *
	 * @param \Elgg\Hook $hook 'search:config', 'search_types'
	 *
	 * @return array
	 */
	public static function getSearchTypes(\Elgg\Hook $hook) {
		
	}

	/**
	 * Returns the search results
	 *
	 * @param \Elgg\Hook $hook 'search:options', 'all'
	 *
	 * @return array
	 */
	public static function getOptions(\Elgg\Hook $hook) {

	}

	/**
	 * Returns the search results
	 *
	 * @param \Elgg\Hook $hook 'search:results', 'all'
	 *
	 * @return array
	 */
	public static function getResults(\Elgg\Hook $hook) {
		
	}

	/**
	 * Returns the search results for users used in the autocomplete
	 *
	 * @param \Elgg\Hook $hook 'autocomplete', 'search_advanced'
	 *
	 * @return array
	 */
	public static function getAutocompleteHelpers(\Elgg\Hook $hook) {
		
		if (elgg_get_plugin_setting('enable_autocomplete_helpers', 'search_advanced') === 'no') {
			return;
		}
				
		$result = $hook->getValue();
		
		$query = $hook->getParam('query');
		
		$owner = null;
		$container = null;
		$type = null;
		$subtype = null;
		
		$route_parts = explode(':', $hook->getParam('route_name', ''));
		
		$entity = get_entity($hook->getParam('entity_guid'));
		if ((elgg_extract(0, $route_parts) !== 'collection') && $entity instanceof \ElggEntity) {
			$type = $entity->getType();
			$subtype = $entity->getSubtype();
			
			$owner = $entity->getOwnerEntity();
			$container = $entity->getContainerEntity();
		} else {
			$page_owner = get_entity($hook->getParam('page_owner_guid'));
			if ($page_owner instanceof \ElggGroup) {
				$container = $page_owner;
			} elseif ($page_owner instanceof \ElggUser) {
				$owner = $page_owner;
			}
		
			if (elgg_extract(0, $route_parts) == 'collection') {
				$type = elgg_extract(1, $route_parts);
				$subtype = elgg_extract(2, $route_parts);
			}
		}
		
		// validate searchable types
		if ($type && $subtype) {
			$searchable_subtypes = get_registered_entity_types($type);
			if (in_array($subtype, $searchable_subtypes)) {
				$result[] = [
					'type' => 'placeholder',
					'content' => self::formatAutocompletePlaceholder([
						'icon' => 'search',
						'text' => $query,
						'info' => elgg_echo('search_advanced:autocomplete:placeholder:type', [elgg_echo("item:{$type}:{$subtype}")]),
					]),
					'href' => elgg_generate_url('default:search', [
						'q' => $hook->getParam('query'),
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
					'q' => $hook->getParam('query'),
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
					'q' => $hook->getParam('query'),
					'container_guid' => $container->guid,
				]),
			];
		}
						
		return $result;
	}

	/**
	 * Returns the 'search all' link in the autocomplete
	 *
	 * @param \Elgg\Hook $hook 'autocomplete', 'search_advanced'
	 *
	 * @return array
	 */
	public static function getAutocompleteSearchAll(\Elgg\Hook $hook) {
		
		$result = $hook->getValue();
	
		if (empty($result)) {
			return;
		}
		
		$result[] = [
			'type' => 'placeholder',
			'content' => self::formatAutocompletePlaceholder([
				'icon' => 'search',
				'text' => $hook->getParam('query'),
				'info' => elgg_echo('search_advanced:autocomplete:placeholder:all'),
			]),
			'href' => elgg_generate_url('default:search', [
				'q' => $hook->getParam('query'),
			]),
		];
						
		return $result;
	}

	/**
	 * Returns the search results for users used in the autocomplete
	 *
	 * @param \Elgg\Hook $hook 'autocomplete', 'search_advanced'
	 *
	 * @return array
	 */
	public static function getAutocompleteUsers(\Elgg\Hook $hook) {
		
		if (elgg_get_plugin_setting('enable_autocomplete_content', 'search_advanced') === 'no') {
			return;
		}
		
		$user_options = [
			'query' => $hook->getParam('query'),
			'limit' => $hook->getParam('limit'),
			'type' => 'user',
		];
		
		$users = elgg_search($user_options);
		$users_count = count($users);
		
		if (empty($users_count)) {
			return;
		}
		
		if ($users_count >= elgg_extract('limit', $user_options)) {
			$user_options['count'] = true;
			$users_count = elgg_search($user_options);
		}
		
		$result = $hook->getValue();
		
		$result[] = [
			'type' => 'placeholder',
			'content' => self::formatAutocompletePlaceholder([
				'text' => elgg_echo('item:user'),
				'count' => $users_count,
			]),
			'href' => elgg_generate_url('default:search', [
				'entity_type' => 'user',
				'search_type' => 'entities',
				'q' => $hook->getParam('query'),
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
	 * @param \Elgg\Hook $hook 'autocomplete', 'search_advanced'
	 *
	 * @return array
	 */
	public static function getAutocompleteGroups(\Elgg\Hook $hook) {
		
		if (!elgg_is_active_plugin('groups')) {
			return;
		}
	
		if (elgg_get_plugin_setting('enable_autocomplete_content', 'search_advanced') === 'no') {
			return;
		}
		
		$group_options = [
			'query' => $hook->getParam('query'),
			'limit' => $hook->getParam('limit'),
			'type' => 'group',
		];
		
		$groups = elgg_search($group_options);
		$groups_count = count($groups);
		
		if (empty($groups_count)) {
			return;
		}
		
		if ($groups_count >= elgg_extract('limit', $group_options)) {
			$group_options['count'] = true;
			$groups_count = elgg_search($group_options);
		}
		
		$result = $hook->getValue();
		
		$result[] = [
			'type' => 'placeholder',
			'content' => self::formatAutocompletePlaceholder([
				'text' => elgg_echo('item:group'),
				'count' => $groups_count,
			]),
			'href' => elgg_generate_url('default:search', [
				'entity_type' => 'group',
				'search_type' => 'entities',
				'q' => $hook->getParam('query'),
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
	 * @param array $params
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
}

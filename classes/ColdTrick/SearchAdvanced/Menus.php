<?php

namespace ColdTrick\SearchAdvanced;

class Menus {
		
	/**
	 * Registers menu type selection menu items
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:search_type_selection'
	 *
	 * @return array
	 */
	public static function registerSearchTypeSelectionItems(\Elgg\Hook $hook) {
		$result = $hook->getValue();
		$service = new \Elgg\Search\Search();
		
		$result[] = \ElggMenuItem::factory([
			'name' => '_selected',
			'text' => elgg_echo('all'),
			'href' => false,
			'deps' => ['search_advanced/type_selection'],
		]);
		$result[] = \ElggMenuItem::factory([
			'name' => 'all',
			'text' => elgg_echo('all'),
			'href' => false,
			'parent_name' => '_selected',
			'data-search-type' => 'all',
			'data-entity-type' => '',
			'data-entity-subtype' => '',
		]);
				
		$types = $service->getTypeSubtypePairs();
		foreach (elgg_extract('user', $types, []) as $subtype) {
			$result[] = \ElggMenuItem::factory([
				'name' => "item:user:{$subtype}",
				'text' => elgg_echo("item:user:{$subtype}"),
				'href' => false,
				'parent_name' => '_selected',
				'data-search-type' => 'entities',
				'data-entity-type' => 'user',
				'data-entity-subtype' => $subtype,
			]);
		}
		foreach (elgg_extract('group', $types, []) as $subtype) {
			$result[] = \ElggMenuItem::factory([
				'name' => "item:group:{$subtype}",
				'text' => elgg_echo("item:group:{$subtype}"),
				'href' => false,
				'parent_name' => '_selected',
				'data-search-type' => 'entities',
				'data-entity-type' => 'group',
				'data-entity-subtype' => $subtype,
			]);
		}
		foreach (elgg_extract('object', $types, []) as $subtype) {
			$result[] = \ElggMenuItem::factory([
				'name' => "item:object:{$subtype}",
				'text' => elgg_echo("item:object:{$subtype}"),
				'href' => false,
				'parent_name' => '_selected',
				'data-search-type' => 'entities',
				'data-entity-type' => 'object',
				'data-entity-subtype' => $subtype,
			]);
		}
		
		$custom_types = $service->getSearchTypes();
		foreach ($custom_types as $type) {
			$result[] = \ElggMenuItem::factory([
				'name' => "search_types:{$type}",
				'text' => elgg_echo("search_types:{$type}"),
				'href' => false,
				'parent_name' => '_selected',
				'data-search-type' => $type,
				'data-entity-type' => '',
				'data-entity-subtype' => '',
			]);
		}
		
		return $result;
	}
	
	/**
	 * Registers menu items related to search results listing
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:search_list'
	 *
	 * @return array
	 */
	public static function registerSearchListItems(\Elgg\Hook $hook) {
		$result = $hook->getValue();
		
		$url = current_page_url();
		$current_list_type = search_advanced_get_list_type();

		$list_compact = (bool) ($current_list_type === 'compact');
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'selected-list-type',
			'text' => $list_compact ? elgg_echo('search_advanced:menu:search_list:compact') : elgg_echo('search_advanced:menu:search_list:entity'),
			'icon' => $list_compact ? 'list' : 'th-list',
			'href' => false,
			'child_menu' => [
				'display' => 'dropdown',
				'data-position' => json_encode([
					'my' => 'right top',
					'at' => 'right bottom',
					'collision' => 'fit fit',
				]),
			],
			'section' => 'alt',
			'title' => elgg_echo('search_advanced:menu:search_list:list:title'),
			'priority' => 999,
		]);
	
		$result[] = \ElggMenuItem::factory([
			'name' => 'other-list-type',
			'text' => !$list_compact ? elgg_echo('search_advanced:menu:search_list:compact') : elgg_echo('search_advanced:menu:search_list:entity'),
			'icon' => !$list_compact ? 'list' : 'th-list',
			'href' => elgg_http_add_url_query_elements($url, [
				'list_type' => $list_compact ? 'list' : 'compact',
			]),
			'section' => 'alt',
			'parent_name' => 'selected-list-type',
		]);
	
		return $result;
	}
}

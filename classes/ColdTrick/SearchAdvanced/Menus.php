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
		
		$types = get_registered_entity_types();
		$custom_types = elgg_trigger_plugin_hook("search_types", "get_types", array(), array());
		
		$result[] = ElggMenuItem::factory([
			"name" => "all",
			"text" => "<a>" . elgg_echo("all") . "</a>",
			"href" => false,
		]);
		$result[] = ElggMenuItem::factory([
			"name" => "item:user",
			"text" => "<a rel='user'>" . elgg_echo("item:user") . "</a>",
			"href" => false,
		]);
		$result[] = ElggMenuItem::factory([
			"name" => "item:group",
			"text" => "<a rel='group'>" . elgg_echo("item:group") . "</a>",
			"href" => false,
		]);
		
		foreach ($types["object"] as $subtype) {
			$result[] = ElggMenuItem::factory([
				"name" => "item:object:$subtype",
				"text" => "<a rel='object " . $subtype . "'>" . elgg_echo("item:object:" . $subtype) . "</a>",
				"href" => false,
				"title" => elgg_echo("item:object:$subtype"),
			]);
		}
		
		foreach ($custom_types as $type) {
			$result[] = ElggMenuItem::factory([
				"name" => "search_types:$type",
				"text" => "<a rel='" . $type . "'>" . elgg_echo("search_types:$type") . "</a>",
				"href" => false,
				"title" => elgg_echo("search_types:$type"),
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
		
		$url = search_advanced_get_search_url();
		$current_list_type = search_advanced_get_list_type();
		$title = elgg_echo('search_advanced:menu:search_list:list:title');
		
		$result[] = ElggMenuItem::factory([
			'name' => 'list',
			'text' => elgg_view_icon('list'),
			'href' => '#',
			'title' => $title,
			'priority' => 999,
		]);
	
		$result[] = ElggMenuItem::factory([
			'name' => 'list_entity',
			'text' => elgg_echo('search_advanced:menu:search_list:entity'),
			'href' => elgg_http_add_url_query_elements($url, ['list_type' => 'entity']),
			'parent_name' => 'list',
			'selected' => ($current_list_type === 'entity'),
			'title' => $title,
		]);
	
		$result[] = ElggMenuItem::factory([
			'name' => 'list_compact',
			'text' => elgg_echo('search_advanced:menu:search_list:compact'),
			'href' => elgg_http_add_url_query_elements($url, ['list_type' => 'compact']),
			'parent_name' => 'list',
			'selected' => ($current_list_type === 'compact'),
			'title' => $title,
		]);
		
		return $result;
	}
}

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
	public static function getAutocompleteUsers(\Elgg\Hook $hook) {
		
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
			'content' => '<label>' . elgg_echo('item:user') . " ({$users_count})</label>",
			'href' => elgg_normalize_url('search?entity_type=user&search_type=entities&q=' . $q),
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
			'content' => '<label>' . elgg_echo('item:group') . ' (' . $groups_count . ')</label>',
			'href' => elgg_normalize_url('search?entity_type=group&search_type=entities&q=' . $q),
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
}

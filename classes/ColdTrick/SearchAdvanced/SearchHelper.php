<?php

namespace ColdTrick\SearchAdvanced;

/**
 * Search helper
 */
class SearchHelper extends \Elgg\Search\Search {
	
	/**
	 * List search results for given search type
	 *
	 * @param string $search_type Search type
	 * @param string $type        Entity type
	 * @param string $subtype     Subtype
	 * @param bool   $count       Count
	 *
	 * @return int|string
	 */
	public function listResults($search_type, $type = null, $subtype = null, $count = false) {
		$current_params = $this->params;
		
		$current_params['search_type'] = $search_type;
		$current_params['type'] = $type;
		$current_params['subtype'] = $subtype;
		
		if (in_array($search_type, ['combined:objects', 'combined:all'])) {
			// fix params for combined searches
			$current_params['limit'] = max((int) get_input('limit'), elgg_get_config('default_limit'));
			$current_params['offset'] = get_input('offset', 0);
			$current_params['pagination'] = true;
			
			unset($current_params['type']);
			unset($current_params['subtype']);
			
			$capabilities = elgg_entity_types_with_capability('searchable');
			
			switch ($search_type) {
				case 'combined:objects':
					$current_params['type_subtype_pairs'] = [
						'object' => elgg_extract('object', $capabilities, []),
					];
					break;
				case 'combined:all':
					$current_params['type_subtype_pairs'] = $capabilities;
					break;
			}
		}
		
		// normalizing current search params so the listing has better awareness
		$current_params = _elgg_services()->search->normalizeOptions($current_params);
				
		switch ($search_type) {
			case 'entities':
				if ($subtype && _elgg_services()->events->hasHandler('search', "{$type}:{$subtype}")) {
					$hook_type = "{$type}:{$subtype}";
				} else {
					$hook_type = $type;
				}
				break;

			default:
				$hook_type = $search_type;
				break;
		}

		$results = [
			'entities' => [],
			'count' => 0,
		];

		$current_params['count'] = true;
		$results['count'] = (int) elgg_search($current_params);
		if ($count) {
			return $results['count'];
		}
		
		if (!empty($results['count'])) {
			unset($current_params['count']);
			$results['entities'] = elgg_search($current_params);
		}
	
		if (empty($results['entities'])) {
			return '';
		}
		
		// empty query placeholder cleanup
		if ($current_params['query'] === Search::QUERY_PLACEHOLDER) {
			unset($current_params['query']);
			unset($current_params['query_parts']);
			unset($current_params['wheres']['search']);
		}
		
		return elgg_view('search/list', [
			'results' => $results,
			'params' => $current_params,
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function initParams(array $params = []) {
		$defaults = [
			'search_filter' => (array) get_input('filter', []),
		];
		
		$params = array_merge($defaults, $params);
		
		parent::initParams($params);
	}
}

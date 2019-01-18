<?php

namespace ColdTrick\SearchAdvanced;

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
	 * @throws \InvalidParameterException
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
			
			switch ($search_type) {
				case 'combined:objects':
					$current_params['type_subtype_pairs'] = [
						'object' => get_registered_entity_types('object'),
					];
					break;
				case 'combined:all':
					$current_params['type_subtype_pairs'] = get_registered_entity_types();
					break;
			}
		}
		
		// normalizing current search params so the listing has better awareness
		$current_params = _elgg_services()->search->normalizeOptions($current_params);
				
		switch ($search_type) {
			case 'entities' :
				if ($subtype && _elgg_services()->hooks->hasHandler('search', "$type:$subtype")) {
					$hook_type = "$type:$subtype";
				} else {
					$hook_type = $type;
				}
				break;

			default :
				$hook_type = $search_type;
				break;
		}

		$results = [
			'entities' => [],
			'count' => 0,
		];

		if (_elgg_services()->hooks->hasHandler('search', $hook_type)) {
			elgg_deprecated_notice("
			'search','$hook_type' plugin hook has been deprecated and may be removed.
			Please consult the documentation for the new core search API
			and update your use of search hooks.
		", '3.0');
			$results = elgg_trigger_plugin_hook('search', $hook_type, $current_params, $results);
			if ($count) {
				return (int) $results['count'];
			}
		} else {
			$current_params['count'] = true;
			$results['count'] = (int) elgg_search($current_params);
			if ($count) {
				return $results['count'];
			}
			if (!empty($results['count'])) {
				unset($current_params['count']);
				$results['entities'] = elgg_search($current_params);
			}
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
	 * {@inheritDoc}
	 * @see \Elgg\Search\Search::initParams()
	 */
	protected function initParams(array $params = []) {
		$defaults = [
			'search_filter' => (array) get_input('filter', []),
		];
		
		$params = array_merge($defaults, $params);
		
		return parent::initParams($params);
	}
}

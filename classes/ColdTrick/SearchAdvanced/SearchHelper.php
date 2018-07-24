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
	 * @param array  $params      Extra params for this specific list
	 *
	 * @return int|string
	 * @throws InvalidParameterException
	 */
	public function listResults($search_type, $type = null, $subtype = null, $count = false, $params = []) {
		$current_params = array_merge($this->params, $params);
		$current_params['search_type'] = $search_type;
		$current_params['type'] = $type;
		$current_params['subtype'] = $subtype;

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

		return elgg_view('search/list', [
			'results' => $results,
			'params' => $current_params,
		]);

	}
}

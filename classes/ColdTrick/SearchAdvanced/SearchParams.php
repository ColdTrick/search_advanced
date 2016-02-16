<?php

namespace ColdTrick\SearchAdvanced;

class SearchParams {
	
	/**
	 * Change the search params for combined searches
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param array  $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|array
	 */
	public static function combinedParams($hook, $type, $return_value, $params) {
		
		$combined_search_type = elgg_extract('combined', $params);
		if (empty($combined_search_type)) {
			return;
		}
		
		switch ($combined_search_type) {
			case 'objects':
				$subtypes = elgg_extract('subtype', $return_value);
				if (empty($subtypes)) {
					return;
				}
				
				if (in_array('groupforumtopic', $subtypes)) {
					// add discussion replies
					$subtypes[] = 'discussion_reply';
				}
				
				if (in_array('page', $subtypes)) {
					// add top pages
					$subtypes[] = 'page_top';
				}
				
				$return_value['subtype'] = $subtypes;
				break;
			case 'all':
				$type_subtype_pairs = elgg_extract('type_subtype_pairs', $return_value);
				if (empty($type_subtype_pairs) || empty($type_subtype_pairs['object'])) {
					return;
				}
				
				if (in_array('groupforumtopic', $type_subtype_pairs['object'])) {
					// add discussion replies
					$type_subtype_pairs['object'][] = 'discussion_reply';
				}
				
				if (in_array('page', $type_subtype_pairs['object'])) {
					// add top pages
					$type_subtype_pairs['object'][] = 'page_top';
				}
				
				$return_value['type_subtype_pairs'] = $type_subtype_pairs;
				
				break;
			default:
				return;
		}
		
		return $return_value;
	}
}

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
		if (elgg_get_plugin_setting('search_hooks_enabled', 'search_advanced') == 'no') {
			return;
		}
		
		
	}

	/**
	 * Returns the search results
	 *
	 * @param \Elgg\Hook $hook 'search:results', 'all'
	 *
	 * @return array
	 */
	public static function getResults(\Elgg\Hook $hook) {
		if (elgg_get_plugin_setting('search_hooks_enabled', 'search_advanced') == 'no') {
			return;
		}
		
		
	}
}

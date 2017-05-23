<?php

namespace ColdTrick\SearchAdvanced;

class PageHandler {
	
	/**
	 * Handle /search_advanced urls
	 *
	 * @param array $page url segments
	 *
	 * @return bool
	 */
	public static function searchAdvanced($page) {
		
		$content = false;
		
		switch ($page[0]) {
			case 'autocomplete':
				$content = elgg_view_resource('search_advanced/autocomplete');
				break;
		}
		
		if (empty($content)) {
			return elgg_error_response();
		}
		
		return elgg_ok_response($content);
	}
	
	/**
	 * Handle /search urls
	 *
	 * @param array $page url segments
	 *
	 * @return bool
	 */
	public static function search($page) {
		
		// if there is no q set, we're being called from a legacy installation
		// it expects a search by tags.
		// actually it doesn't, but maybe it should.
		// maintain backward compatibility
		if (!get_input('q', get_input('tag', NULL)) && isset($page[0])) {
			set_input('q', $page[0]);
			set_input('search_type', 'tags');
		}
		
		search_advanced_update_list_type();
		
		return elgg_ok_response(elgg_view_resource('search/index'));
	}
}

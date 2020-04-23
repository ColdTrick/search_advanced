<?php

namespace ColdTrick\SearchAdvanced;

class Views {
	
	/**
	 * Updates user preference for list type if request type changes
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'resources/search/index'
	 *
	 * @return array
	 */
	public static function updateListType(\Elgg\Hook $hook) {
		// check the user preference for list_type preference change
		if (!elgg_is_logged_in()) {
			return;
		}
		
		$list_type = get_input('list_type');
		if (empty($list_type)) {
			return;
		}
		
		$current_list_type = elgg_get_plugin_user_setting('list_type', elgg_get_logged_in_user_guid(), 'search_advanced');
		if ($current_list_type !== $list_type) {
			// save new preference
			elgg_set_plugin_user_setting('list_type', $list_type, elgg_get_logged_in_user_guid(), 'search_advanced');
		}
	}
	
	/**
	 * Shows the loader if needed
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'resources/search/index'
	 *
	 * @return array
	 */
	public static function showSearchIndexLoader(\Elgg\Hook $hook) {
		if (elgg_is_xhr()) {
			return;
		}

		if (elgg_get_plugin_setting('search_with_loader', 'search_advanced') !== 'yes') {
			return;
		}
		
		$loader = elgg_view('output/url', [
			'class' => 'hidden search-advanced-load-content',
			'href' => elgg_http_add_url_query_elements(current_page_url(), ['loader' => 1]),
		]);
					
		$output = elgg_view_page(elgg_echo('search'), [
			'content' => $loader,
			'title' => false,
		]);
		
		return ['__view_output' => $output];
	}
}

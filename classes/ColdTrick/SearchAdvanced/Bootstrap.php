<?php

namespace ColdTrick\SearchAdvanced;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		$this->registerViews();
		$this->registerHooks();
	}
	
	protected function registerViews() {
// 		elgg_extend_view('css/elgg', 'css/search_advanced/site');
		elgg_extend_view('js/elgg', 'js/search_advanced/site.js');
		elgg_extend_view('js/elgg', 'js/search_advanced/ajax_submit.js');
	}
	
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('register', 'menu:search_type_selection', '\ColdTrick\SearchAdvanced\SearchParams::registerSearchTypeSelectionItems');
		$hooks->registerHandler('register', 'menu:search_list', '\ColdTrick\SearchAdvanced\SearchParams::registerSearchListItems');
		$hooks->registerHandler('search_params', 'search:combined', '\ColdTrick\SearchAdvanced\SearchParams::combinedParams');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::saveArrayTypeSetting');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::flushCache');
		$hooks->registerHandler('view_vars', 'resources/search/index', '\ColdTrick\SearchAdvanced\Views::updateListType');
		
		// register search advanced search hooks
		$hooks->registerHandler('search', 'object', 'search_advanced_objects_hook');
		$hooks->registerHandler('search', 'user', 'search_advanced_users_hook');
		$hooks->registerHandler('search', 'group', 'search_advanced_groups_hook');
		$hooks->registerHandler('search', 'combined:all', 'search_advanced_combined_all_hook');
	}
}

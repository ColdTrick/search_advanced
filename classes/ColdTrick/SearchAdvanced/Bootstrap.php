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
		elgg_extend_view('css/elgg', 'css/search_advanced.css');
		elgg_extend_view('js/elgg', 'js/search_advanced/site.js');
		elgg_extend_view('js/elgg', 'js/search_advanced/ajax_submit.js');
	}
	
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('autocomplete', 'search_advanced', '\ColdTrick\SearchAdvanced\Search::getAutocompleteUsers', 100);
		$hooks->registerHandler('autocomplete', 'search_advanced', '\ColdTrick\SearchAdvanced\Search::getAutocompleteGroups', 200);
		$hooks->registerHandler('elgg.data', 'page', '\ColdTrick\SearchAdvanced\Search::getAutocompleteHelpers');
		$hooks->registerHandler('register', 'menu:search_type_selection', '\ColdTrick\SearchAdvanced\Menus::registerSearchTypeSelectionItems');
		$hooks->registerHandler('register', 'menu:filter:search', '\ColdTrick\SearchAdvanced\Menus::registerSearchListItems');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::saveArrayTypeSetting');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::flushCache');
		$hooks->registerHandler('search:params', 'all', '\ColdTrick\SearchAdvanced\Search::allowEmptyQuery');
		$hooks->registerHandler('search:params', 'all', '\ColdTrick\SearchAdvanced\Search::allowEmptyQueryWithProfileSearch', 400);
		$hooks->registerHandler('search:config', 'search_types', '\ColdTrick\SearchAdvanced\Search::getSearchTypes');
		$hooks->registerHandler('search:fields', 'group', __NAMESPACE__ . '\Search::cleanupGroupMetadataFields', 999);
		$hooks->registerHandler('search:fields', 'user', __NAMESPACE__ . '\Search::cleanupUserMetadataFields', 999);
		$hooks->registerHandler('search:options', 'user', __NAMESPACE__ . '\Search::sanitizeProfileFieldFilter', 1);
		$hooks->registerHandler('search:options', 'user', __NAMESPACE__ . '\Search::searchUserProfileFilter');
		$hooks->registerHandler('view_vars', 'resources/search/index', '\ColdTrick\SearchAdvanced\Views::showSearchIndexLoader');
		$hooks->registerHandler('view_vars', 'resources/search/index', '\ColdTrick\SearchAdvanced\Views::updateListType');
	}
}

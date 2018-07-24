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
		elgg_extend_view('css/elgg', 'css/search_advanced.css');
		elgg_extend_view('js/elgg', 'js/search_advanced/site.js');
		elgg_extend_view('js/elgg', 'js/search_advanced/ajax_submit.js');
	}
	
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('register', 'menu:search_type_selection', '\ColdTrick\SearchAdvanced\Menus::registerSearchTypeSelectionItems');
		$hooks->registerHandler('register', 'menu:search_list', '\ColdTrick\SearchAdvanced\Menus::registerSearchListItems');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::saveArrayTypeSetting');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::flushCache');
		$hooks->registerHandler('search:params', 'all', '\ColdTrick\SearchAdvanced\Search::getParams');
		$hooks->registerHandler('search:config', 'type_subtype_pairs', '\ColdTrick\SearchAdvanced\Search::getTypeSubtypePairs');
		$hooks->registerHandler('search:config', 'search_types', '\ColdTrick\SearchAdvanced\Search::getSearchTypes');
		$hooks->registerHandler('search:fields', 'combined:all', \Elgg\Search\UserSearchFieldsHandler::class);
		$hooks->registerHandler('search:fields', 'combined:all', \Elgg\Search\ObjectSearchFieldsHandler::class);
		$hooks->registerHandler('search:fields', 'combined:all', \Elgg\Search\GroupSearchFieldsHandler::class);
		$hooks->registerHandler('search:fields', 'combined:objects', \Elgg\Search\ObjectSearchFieldsHandler::class);
		$hooks->registerHandler('search:options', 'all', '\ColdTrick\SearchAdvanced\Search::getOptions');
		$hooks->registerHandler('search:results', 'all', '\ColdTrick\SearchAdvanced\Search::getResults');
		$hooks->registerHandler('view_vars', 'resources/search/index', '\ColdTrick\SearchAdvanced\Views::showSearchIndexLoader');
		$hooks->registerHandler('view_vars', 'resources/search/index', '\ColdTrick\SearchAdvanced\Views::updateListType');
	}
}

<?php

namespace ColdTrick\SearchAdvanced;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		
		elgg_register_page_handler('search_advanced', '\ColdTrick\SearchAdvanced\PageHandler::searchAdvanced');
		elgg_register_page_handler('search', '\ColdTrick\SearchAdvanced\PageHandler::search');
		
		search_advanced_prepare_search_hooks();
				
		$this->registerViews();
		$this->registerHooks();
	}
	
	protected function registerViews() {
// 		elgg_extend_view('css/elgg', 'css/search_advanced/site');
		elgg_extend_view('js/elgg', 'js/search_advanced/site.js');
		elgg_extend_view('js/elgg', 'js/search_advanced/ajax_submit.js');
		
		elgg_define_js('browserstate-history', [
			'src' => '/mod/search_advanced/vendors/browserstate-history.js/jquery.history.js',
			'exports' => 'History',
		]);
	}
	
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('register', 'menu:search_type_selection', '\ColdTrick\SearchAdvanced\SearchParams::registerSearchTypeSelectionItems');
		$hooks->registerHandler('register', 'menu:search_list', '\ColdTrick\SearchAdvanced\SearchParams::registerSearchListItems');
		$hooks->registerHandler('search_params', 'search:combined', '\ColdTrick\SearchAdvanced\SearchParams::combinedParams');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::saveArrayTypeSetting');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::flushCache');
	}
}

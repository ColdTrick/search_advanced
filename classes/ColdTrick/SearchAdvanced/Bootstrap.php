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
							
		// widgets
		elgg_register_widget_type('search', elgg_echo('search'), elgg_echo('search'), ['profile', 'dashboard', 'index', 'groups'], true);
		elgg_register_widget_type('search_user', elgg_echo('search_advanced:widgets:search_user:title'), elgg_echo('search_advanced:widgets:search_user:description'), ['dashboard', 'index']);
				
		$this->registerViews();
		$this->registerEvents();
		$this->registerHooks();
	}
	
	protected function registerViews() {
		elgg_extend_view('css/elgg', 'css/search_advanced/site');
		elgg_extend_view('js/elgg', 'js/search_advanced/site.js');
		
		elgg_define_js('browserstate-history', [
			'src' => '/mod/search_advanced/vendors/browserstate-history.js/jquery.history.js',
			'exports' => 'History',
		]);
		
		elgg_extend_view('page/elements/foot', 'search_advanced/foot', 400);
	}
	
	protected function registerEvents() {

	}
	
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('register', 'menu:search_type_selection', 'search_advanced_register_menu_type_selection');
		$hooks->registerHandler('register', 'menu:search_list', 'search_advanced_register_menu_list');
		$hooks->registerHandler('search_params', 'search:combined', '\ColdTrick\SearchAdvanced\SearchParams::combinedParams');
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\SearchAdvanced\Settings::saveArrayTypeSetting');
	}
}

<?php

use ColdTrick\SearchAdvanced\Bootstrap;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'settings' => [
		'multi_tag_separator' => 'comma',
		'enable_multi_tag' => 'no',
		'combine_search_results' => 'no',
		'search_with_loader' => 'no',
		'search_hooks_enabled' => 'yes',
		'query_required' => 'yes',
	],
	'routes' => [
		
	],
	'widgets' => [
		'search' => [
			'name' => elgg_echo('search'),
			'description' => elgg_echo('search'),
			'context' => ['admin', 'profile', 'dashboard', 'index', 'groups'],
			'multiple' => true,
		],
		'search_user' => [
			'context' => ['admin', 'dashboard', 'index'],
		],
	],
];

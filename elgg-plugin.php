<?php

use ColdTrick\SearchAdvanced\Bootstrap;
use Elgg\Router\Middleware\AjaxGatekeeper;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'settings' => [
		'combine_search_results' => 'no',
		'search_with_loader' => 'no',
		'query_required' => 'yes',
		'enable_search_type_selection' => 'yes',
		'enable_autocomplete' => 'yes',
		'enable_autocomplete_content' => 'yes',
		'enable_autocomplete_helpers' => 'yes',
	],
	'routes' => [
		'autocomplete:search_advanced' => [
			'path' => 'search_advanced/autocomplete',
			'resource' => 'search_advanced/autocomplete',
			'middleware' => [
				AjaxGatekeeper::class,
			],
		],
	],
	'widgets' => [
		'search' => [
			'context' => ['admin', 'profile', 'dashboard', 'index', 'groups'],
			'multiple' => true,
		],
		'search_user' => [
			'context' => ['admin', 'dashboard', 'index', 'groups'],
		],
	],
];

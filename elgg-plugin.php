<?php

use Elgg\Router\Middleware\AjaxGatekeeper;

return [
	'plugin' => [
		'version' => '10.0.1',
		'dependencies' => [
			'search' => [
				'position' => 'after',
			],
			'members' => [
				'position' => 'after',
				'must_be_active' => false,
			],
		],
	],
	'settings' => [
		'combine_search_results' => 'no',
		'query_required' => 'yes',
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
	'events' => [
		'autocomplete' => [
			'search_advanced' => [
				'\ColdTrick\SearchAdvanced\Search::getAutocompleteUsers' => ['priority' => 100],
				'\ColdTrick\SearchAdvanced\Search::getAutocompleteGroups' => ['priority' => 200],
			],
		],
		'elgg.data' => [
			'page' => [
				'\ColdTrick\SearchAdvanced\Search::getAutocompleteHelpers' => [],
			],
		],
		'index_entity_type_subtypes' => [
			'opensearch' => [
				'\ColdTrick\SearchAdvanced\Plugins\OpenSearch::unregisterPreventedSearchTypes' => [],
			],
		],
		'ready' => [
			'system' => [
				'\ColdTrick\SearchAdvanced\Settings::disableSearchables' => [],
			],
		],
		'search:config' => [
			'search_types' => [
				'\ColdTrick\SearchAdvanced\Search::getSearchTypes' => [],
			],
		],
		'search:fields' => [
			'group' => [
				'\ColdTrick\SearchAdvanced\Search::cleanupGroupMetadataFields' => ['priority' => 999],
			],
			'user' => [
				'\ColdTrick\SearchAdvanced\Search::cleanupUserMetadataFields' => ['priority' => 999],
			],
		],
		'search:options' => [
			'user' => [
				'\ColdTrick\SearchAdvanced\Search::sanitizeProfileFieldFilter' => ['priority' => 1],
				'\ColdTrick\SearchAdvanced\Search::searchUserProfileFilter' => [],
			],
		],
		'search:params' => [
			'all' => [
				'\ColdTrick\SearchAdvanced\Search::allowEmptyQuery' => [],
				'\ColdTrick\SearchAdvanced\Search::allowEmptyQueryWithProfileSearch' => ['priority' => 400],
			],
		],
		'setting' => [
			'plugin' => [
				'\ColdTrick\SearchAdvanced\Settings::saveArrayTypeSetting' => [],
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
	'view_extensions' => [
		'elgg.css' => [
			'css/search_advanced.css' => [],
		],
	],
];

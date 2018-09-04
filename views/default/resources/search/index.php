<?php

/**
 * Elgg search page
 */

// Search supports RSS
elgg_register_rss_link();

// This magic is needed to support /search/<query>
// but have /search/<query1>?q=<query2> as <query2> be the main search query
set_input('q', get_input('q', elgg_extract('route_query', $vars, null, false)));

// setting list_type input so elgg_view_entity_list can use it
set_input('list_type', get_input('list_type', search_advanced_get_list_type()));

$service = new \ColdTrick\SearchAdvanced\SearchHelper();
$params = $service->getParams();

$container_guid = elgg_extract('container_guid', $params);
if ($container_guid && !is_array($container_guid)) {
	elgg_entity_gatekeeper($container_guid);
	
	elgg_set_page_owner_guid($container_guid);
}

$query = elgg_extract('query', $params);

$params['inline_form'] = true;

$form = elgg_view_form('search', [
	'action' => elgg_normalize_url('search'),
	'method' => 'get',
	'disable_security' => true,
], $params);

if (empty($query) && $query != "0") {
	// display a search form if there is no query
	$layout = elgg_view_layout('content', [
		'title' => elgg_echo('search'),
		'content' => $form,
		'filter' => '',
	]);

	echo elgg_view_page(elgg_echo('search'), $layout);

	return;
}

$combine_results = elgg_get_plugin_setting('combine_search_results', 'search_advanced');

$use_type = function ($search_type, $type = null, $subtype = null) use ($params, $combine_results) {

	if ($params['search_type'] == 'all') {
		if ($search_type === 'entities') {
			if ($combine_results === 'all') {
				return false;
			} elseif ($combine_results === 'objects' && ($type == 'object')) {
				return false;
			}
		}
		
		return true;
	}

	switch ($params['search_type']) {
		case 'entities' :
			if ($params['type'] && $params['type'] != $type) {
				return false;
			} else if ($params['subtype'] && $params['subtype'] !== $subtype) {
				return false;
			}

			return true;

		// custom search type
		default :
			return $params['search_type'] == $search_type;
	}
};

$total = 0;
$results = '';

$register_menu_items = elgg_extract('register_menu_items', $vars, true);

$types = $service->getTypeSubtypePairs();
foreach ($types as $type => $subtypes) {
	if (empty($subtypes) || !is_array($subtypes)) {
		continue;
	}
	
	foreach ($subtypes as $subtype) {
		if ($register_menu_items) {
			$count = $service->listResults('entities', $type, $subtype, true);
			$total += $count;
			elgg_register_menu_item('page', [
				'name' => "item:$type:$subtype",
				'text' => elgg_echo("item:$type:$subtype"),
				'href' => elgg_http_add_url_query_elements('search', [
					'q' => $params['query'],
					'entity_type' => $type,
					'entity_subtype' => $subtype,
					'owner_guid' => $params['owner_guid'],
					'search_type' => 'entities',
				]),
				'badge' => $count,
			]);
		}
		
		if ($use_type('entities', $type, $subtype)) {
			if (!$register_menu_items) {
				$count = $service->listResults('entities', $type, $subtype, true);
				$total += $count;
			}
			$results .= $service->listResults('entities', $type, $subtype);
		}
	}
}

if (($combine_results !== 'no') && ($params['search_type'] === 'all')) {
	if ($combine_results === 'objects') {
		$object_subtypes = elgg_extract('object', $types);
		if (!empty($object_subtypes)) {
			$extra_params = [
				'type_subtype_pairs' => ['object' => $object_subtypes],
			];
			
			$count = $service->listResults('combined:objects', null, null, true, $extra_params);
			$total += $count;
			
			$results .= $service->listResults('combined:objects', null, null, false, $extra_params);
		}
	} else {
		$extra_params = [
			'type_subtype_pairs' => $types,
		];
		
		$count = $service->listResults('combined:all', null, null, true, $extra_params);
		$total += $count;
			
		$results .= $service->listResults('combined:all', null, null, false, $extra_params);
	}
}

$custom_types = $service->getSearchTypes();
foreach ($custom_types as $search_type) {
	if ($register_menu_items) {
		$count = $service->listResults($search_type, null, null, true);
		$total += $count;
		elgg_register_menu_item('page', [
			'name' => "search_types:$type",
			'text' => elgg_echo("search_types:$type"),
			'href' => elgg_http_add_url_query_elements('search', [
				'q' => $params['query'],
				'search_type' => $type,
			]),
			'badge' => $count,
		]);
	}

	if ($use_type($search_type)) {
		if (!$register_menu_items) {
			$count = $service->listResults($search_type, null, null, true);
			$total += $count;
		}
		$results .= $service->listResults($search_type);
	}
}

if ($register_menu_items) {
	elgg_register_menu_item('page', [
		'name' => 'all',
		'text' => elgg_echo('all'),
		'href' => elgg_http_add_url_query_elements('search', [
			'q' => $params['query'],
			'owner_guid' => $params['owner_guid'],
			'search_type' => 'all',
		]),
		'badge' => $total,
		'priority' => 1,
	]);
}

if (empty($results)) {
	$results = elgg_format_element('p', [
		'class' => 'elgg-no-results',
	], elgg_echo('notfound'));
}

$filter = elgg_view('page/layouts/elements/filter', ['filter_id' => 'search']);

$title = elgg_view('search/title', [
	'query' => $query,
	'service' => $service,
	'count' => $total,
]);

$layout = elgg_view_layout('content', [
	'title' => $title,
	'content' => $results,
	'filter' => $form . $filter,
]);

echo elgg_view_page(elgg_echo('search'), $layout);

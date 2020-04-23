<?php
/**
 * Elgg search page
 */

use ColdTrick\SearchAdvanced\Search;

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

$filter = elgg_view('page/layouts/elements/filter', ['filter_id' => 'search']);

$form = '';
if (elgg_extract('show_inline_form', $vars, true)) {
	$params['inline_form'] = true;
	
	$form = elgg_view_form('search', [
		'action' => elgg_normalize_url('search'),
		'method' => 'get',
		'disable_security' => true,
	], $params);
}

if (elgg_is_empty($query)) {
	// display a search form if there is no query
	echo elgg_view_page(elgg_echo('search'), [
		'content' => elgg_view('page/components/no_results', ['no_results' => elgg_echo('search_advanced:results:empty_query')]),
		'filter' => $form . $filter,
		'filter_id' => 'search',
	]);

	return;
}

// undo query placeholder
if ($query === Search::QUERY_PLACEHOLDER) {
	$query = null;
	unset($params['query']);
	unset($params['query_parts']);
	unset($params['wheres']['search']);
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
$result_total = 0;
$results = '';

$register_menu_items = elgg_extract('register_menu_items', $vars, true);

$types = $service->getTypeSubtypePairs();
foreach ($types as $type => $subtypes) {
	if (empty($subtypes) || !is_array($subtypes)) {
		continue;
	}
	
	foreach ($subtypes as $subtype) {
		$count = 0;
		if ($register_menu_items) {
			$count = $service->listResults('entities', $type, $subtype, true);
			$total += $count;
			elgg_register_menu_item('page', [
				'name' => "item:$type:$subtype",
				'text' => elgg_echo("item:$type:$subtype"),
				'href' => elgg_http_add_url_query_elements('search', [
					'q' => elgg_extract('query', $params),
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
			$result_total += $count;
		}
	}
}

$custom_types = $service->getSearchTypes();
foreach ($custom_types as $search_type) {
	$count = 0;
	if ($register_menu_items) {
		$count = $service->listResults($search_type, null, null, true);
		
		if (!in_array($search_type, ['combined:objects', 'combined:all'])) {
			$total += $count;
			elgg_register_menu_item('page', [
				'name' => "search_types:{$search_type}",
				'text' => elgg_echo("search_types:{$search_type}"),
				'href' => elgg_http_add_url_query_elements('search', [
					'q' => $params['query'],
					'search_type' => $search_type,
				]),
				'badge' => $count,
			]);
		}
	}

	if ($use_type($search_type)) {
		if (!$register_menu_items) {
			$count = $service->listResults($search_type, null, null, true);
			$total += $count;
		}
		
		$results .= $service->listResults($search_type);
		$result_total += $count;
	}
}

if ($register_menu_items) {
	elgg_register_menu_item('page', [
		'name' => '_all',
		'text' => elgg_echo('all'),
		'href' => elgg_http_add_url_query_elements('search', [
			'q' => elgg_extract('query', $params),
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

if (get_input('widget_search')) {
	echo $results;
	return;
}

$title = elgg_view('search/title', [
	'query' => $query,
	'service' => $service,
	'count' => $result_total,
]);

echo elgg_view_page(elgg_echo('search'), [
	'title' => $title,
	'content' => $results,
	'filter' => $form . $filter,
	'filter_id' => 'search',
]);

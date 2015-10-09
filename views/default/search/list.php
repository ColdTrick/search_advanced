<?php
/**
 * List a section of search results corresponding in a particular type/subtype
 * or search type (comments for example)
 *
 * @uses $vars['results'] Array of data related to search results including:
 *                          - 'entities' Array of entities to be displayed
 *                          - 'count'    Total number of results
 * @uses $vars['params']  Array of parameters including:
 *                          - 'type'        Entity type
 *                          - 'subtype'     Entity subtype
 *                          - 'search_type' Type of search: 'entities', 'comments', 'tags'
 *                          - 'offset'      Offset in search results
 *                          - 'limit'       Number of results per page
 *                          - 'pagination'  Display pagination?
 */

$entities = $vars['results']['entities'];
$count = $vars['results']['count'] - count($entities);

if (!is_array($entities) || !count($entities)) {
	return FALSE;
}

$default_params = [
	'q' => $vars['params']['query'],
	'entity_type' => $vars['params']['type'],
	'entity_subtype' => $vars['params']['subtype'],
	'limit' => $vars['params']['limit'],
	'offset' => $vars['params']['offset'],
	'search_type' => $vars['params']['search_type'],
	'sort' => $vars['params']['sort'],
	'order' => $vars['params']['order'],
	'container_guid' => $vars['params']['container_guid'],
	'owner_guid' => $vars['params']['owner_guid'],
];

$query_params = (array) elgg_extract('query_params', $vars, []);
$query_params = array_merge($default_params, $query_params);

$url = search_advanced_get_search_url();

$more_items = $vars['results']['count'] - ($vars['params']['offset'] + $vars['params']['limit']);

// get pagination
if (array_key_exists('pagination', $vars['params']) && $vars['params']['pagination']) {
	$nav = elgg_view('navigation/pagination', array(
		'base_url' => $url,
		'offset' => $vars['params']['offset'],
		'count' => $vars['results']['count'],
		'limit' => $vars['params']['limit'],
	));
	$show_more = false;
} else {
	// faceted search page so no pagination
	$nav = '';
	$show_more = $more_items > 0;
}

// figure out what we're dealing with.
$type = elgg_extract('entity_type', $query_params);
$subtype = elgg_extract('entity_subtype', $query_params);
$search_type = elgg_extract('search_type', $query_params);

if (elgg_language_key_exists("item:$type:$subtype")) {
	$title = elgg_echo("item:$type:$subtype");
} elseif ($type == 'object') {
	$title = elgg_echo("search_advanced:content:title");
} elseif ($type) {
	$title = elgg_echo("item:$type");
} elseif (in_array('entities', [$type, $search_type])) {
	$title = elgg_echo("search_advanced:content:title");
} else {
	$title = elgg_echo('search:unknown_entity');
}

// allow overrides for titles
if (elgg_language_key_exists("search_types:$search_type")) {
	$title = elgg_echo("search_types:$search_type");
}

$list_items = '';
foreach ($entities as $entity) {
	$view = search_advanced_get_search_view([
		'type' => $entity->type,
		'subtype' => $entity->getSubtype(),
		'search_type' => $search_type
	], search_advanced_get_list_type());
	
	if (empty($view)) {
		continue;
	}
	
	$entity_params = [
		'entity' => $entity,
		'params' => $vars['params'],
		'results' => $vars['results']
	];
	
	$list_item = elgg_view('search_advanced/search/floating_tag', $entity_params);
	$list_item .= elgg_view($view, $entity_params);
	
	$list_items .= elgg_format_element('li', [
		'id' => "elgg-{$entity->getType()}-{$entity->getGUID()}",
		'class' => 'elgg-item'
	], $list_item);
}

$header = elgg_format_element('h3', [], $title);
if ($show_more) {
	$url = search_advanced_get_search_url([
		'limit' => null,
		'search_type' => $search_type,
		'entity_type' => $type,
		'entity_subtype' => $subtype,
	]);
	$more_link = elgg_view('output/url', [
		'href' => $url,
		'text' => elgg_echo('search:more', array($count, $title)),
		'class' => 'search-more float-alt'
	]);
	$header = $more_link . $header;
}

$body = elgg_format_element('ul', ['class' => 'elgg-list search-list search-list-type-' . search_advanced_get_list_type()], $list_items);
$body .= $nav;

echo elgg_view_module('info', '', $body, ['header' => $header]);
<?php
/**
 * Search form
 *
 * @uses $vars['value'] Current search query
 */

$entity_guid = 0;
$page_owner_guid = elgg_get_page_owner_guid();

$route = _elgg_services()->request->getRoute();
$route_name = '';
if ($route) {
	$entity_guid = elgg_extract('guid', $route->getMatchedParameters());
	$route_name = $route->getName();
}

$enable_autocomplete = (bool) (elgg_get_plugin_setting('enable_autocomplete', 'search_advanced') === 'yes');
$enable_autocomplete = elgg_extract('search_autocomplete', $vars, $enable_autocomplete);
if ($enable_autocomplete) {
	elgg_require_js('search_advanced/autocomplete');
}

$value = elgg_extract('value', $vars, get_input('q', get_input('tag')));

echo elgg_view_field([
	'#type' => 'text',
	'class' => 'search-input',
	'size' => '21',
	'name' => 'q',
	'autocapitalize' => 'off',
	'autocorrect' => 'off',
	'autocomplete' => $enable_autocomplete ? 'off' : null,
	'required' => true,
	'value' => _elgg_get_display_query($value),
	'placeholder' => elgg_echo('search_advanced:searchbox'),
	'data-entity-guid' => $entity_guid,
	'data-page-owner-guid' => $page_owner_guid,
	'data-route-name' => $route_name,
]);

if (!elgg_extract('inline_form', $vars, false) && (elgg_get_plugin_setting('enable_search_type_selection', 'search_advanced') === 'yes')) {
	echo elgg_view_menu('search_type_selection');
}
unset($vars['inline_form']);

echo elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_view_icon('search'),
]);

$values = [
	'entity_subtype' => get_input('entity_subtype', ''),
	'entity_type' => get_input('entity_type', ''),
	'owner_guid' => get_input('owner_guid'),
	'container_guid' => get_input('container_guid'),
	'search_type' => get_input('search_type', 'all'),
];

foreach ($values as $name => $value) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => $name,
		'value' => $value,
	]);
}


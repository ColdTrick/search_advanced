<?php
/**
 * Search form
 *
 * @uses $vars['value'] Current search query
 */

if (elgg_extract('search_autocomplete', $vars, true)) {
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
	'required' => true,
	'value' => _elgg_get_display_query($value),
	'placeholder' => elgg_echo('search_advanced:searchbox'),
]);

if (!elgg_extract('inline_form', $vars, false)) {
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


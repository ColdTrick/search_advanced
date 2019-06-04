<?php
/**
 * Search form
 *
 * @uses $vars['value'] Current search query
 */

$is_inline_form = elgg_extract('inline_form', $vars, false);

$enable_autocomplete = (bool) (elgg_get_plugin_setting('enable_autocomplete', 'search_advanced') === 'yes');
$enable_autocomplete = elgg_extract('search_autocomplete', $vars, $enable_autocomplete);
if (!$is_inline_form && $enable_autocomplete) {
	elgg_require_js('search_advanced/autocomplete');
}

$value = elgg_extract('value', $vars, get_input('q', get_input('tag')));

echo elgg_view_field([
	'#type' => 'text',
	'type' => 'search',
	'class' => 'search-input',
	'size' => '21',
	'name' => 'q',
	'autocapitalize' => 'off',
	'autocorrect' => 'off',
	'autocomplete' => $enable_autocomplete ? 'off' : null,
	'required' => true,
	'value' => _elgg_get_display_query($value),
	'placeholder' => elgg_echo('search_advanced:searchbox'),
]);

if (!$is_inline_form && (elgg_get_plugin_setting('enable_search_type_selection', 'search_advanced') === 'yes')) {
	echo elgg_view_menu('search_type_selection');
}
unset($vars['inline_form']);

echo elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_view_icon('search'),
]);

$values = [
	'entity_subtype' => get_input('entity_subtype'),
	'entity_type' => get_input('entity_type'),
	'owner_guid' => get_input('owner_guid'),
	'container_guid' => get_input('container_guid'),
	'search_type' => get_input('search_type', 'all'),
];

foreach ($values as $name => $value) {
	if (!isset($value)) {
		continue;
	}
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => $name,
		'value' => $value,
	]);
}

$filter = (array) get_input('filter');
if (!empty($filter)) {
	foreach ($filter as $key => $value) {
		if ($key === 'profile_fields') {
			// don't leave profile fields filter intact when submitting the search form
			continue;
		}
		
		if (is_array($value)) {
			foreach ($value as $sub_key => $sub_value) {
				echo elgg_view_field([
					'#type' => 'hidden',
					'name' => "filter[$key][$sub_key]",
					'value' => $sub_value,
				]);
			}
		} else {
			echo elgg_view_field([
				'#type' => 'hidden',
				'name' => "filter[$key]",
				'value' => $value,
			]);
		}
	}
}

<?php

$plugin = elgg_extract('entity', $vars);

$yesno_options = [
	'yes' => elgg_echo('option:yes'),
	'no' => elgg_echo('option:no')
];
$noyes_options = array_reverse($yesno_options);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:filter_position'),
	'#help' => elgg_echo('search_advanced:settings:filter_position:info'),
	
	'name' => 'params[filter_position]',
	'options_values' => [
		'content' => elgg_echo('search_advanced:settings:filter_position:content'),
		'sidebar' => elgg_echo('search_advanced:settings:filter_position:sidebar'),
	],
	'value' => $plugin->filter_position,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:search_types_menu_format'),
	'#help' => elgg_echo('search_advanced:settings:search_types_menu_format:info'),
	
	'name' => 'params[search_types_menu_format]',
	'options_values' => [
		'menu' => elgg_echo('search_advanced:settings:search_types_menu_format:menu'),
		'dropdown' => elgg_echo('search_advanced:settings:search_types_menu_format:dropdown'),
	],
	'value' => $plugin->search_types_menu_format,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:combine_search_results'),
	'#help' => elgg_echo('search_advanced:settings:combine_search_results:info'),

	'name' => 'params[combine_search_results]',
	'options_values' => [
		'no' => elgg_echo('option:no'),
		'objects' => elgg_echo('search_advanced:settings:combine_search_results:objects'),
		'all' => elgg_echo('search_advanced:settings:combine_search_results:all'),
	],
	'value' => $plugin->combine_search_results,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:query_required'),
	'#help' => elgg_echo('search_advanced:settings:query_required:help'),

	'name' => 'params[query_required]',
	'options_values' => $yesno_options,
	'value' => $plugin->query_required,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:search_with_loader'),
	'#help' => elgg_echo('search_advanced:settings:search_with_loader:info'),
	
	'name' => 'params[search_with_loader]',
	'options_values' => $noyes_options,
	'value' => $plugin->search_with_loader,
]);

// search hooks settings
echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:search_hooks_enabled'),
	'#help' => elgg_echo('search_advanced:settings:search_hooks_enabled:info'),
	
	'name' => 'params[search_hooks_enabled]',
	'options_values' => $yesno_options,
	'value' => $plugin->search_hooks_enabled,
]);

echo '<div class="mls plm mbm elgg-divide-left">';

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:enable_multi_tag'),
	'#help' => elgg_echo('search_advanced:settings:enable_multi_tag:info'),
	
	'name' => 'params[enable_multi_tag]',
	'options_values' => $noyes_options,
	'value' => $plugin->enable_multi_tag,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:settings:multi_tag_separator'),
	'#help' => elgg_echo('search_advanced:settings:multi_tag_separator:info'),
	
	'name' => 'params[multi_tag_separator]',
	'options_values' => [
		'comma' => elgg_echo('search_advanced:settings:multi_tag_separator:comma'),
		'space' => elgg_echo('search_advanced:settings:multi_tag_separator:space'),
	],
	'value' => $plugin->multi_tag_separator,
]);

echo '</div>';

$body = '';

$body .= elgg_format_element('div',['class' => 'elgg-admin-notices'], elgg_autop(elgg_echo('search_advanced:settings:profile_fields:disclaimer')));

if (elgg_is_active_plugin('groups')) {
	elgg_require_js('search_advanced/settings');

	$body .= elgg_view('navigation/tabs', [
		'tabs' => [
			[
				'text' => elgg_echo('search_advanced:settings:profile_fields:user'),
				'href' => '#',
				'selected' => true,
			],
			[
				'text' => elgg_echo('search_advanced:settings:profile_fields:group'),
				'href' => '#',
			],
		],
		'class' => 'search-advanced-settings-tabs',
	]);
}

$body .= elgg_format_element('div', ['class'=> 'search-advanced-settings-profile-fields'], elgg_view('search_advanced/settings/user_profile_fields', $vars));

if (elgg_is_active_plugin('groups')) {
	$body .= elgg_format_element('div', ['class'=> 'search-advanced-settings-profile-fields hidden'], elgg_view('search_advanced/settings/group_profile_fields', $vars));
}

echo elgg_view_module('inline', elgg_echo('search_advanced:settings:profile_fields'), $body);

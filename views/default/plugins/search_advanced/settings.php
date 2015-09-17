<?php

$plugin = elgg_extract('entity', $vars);

$yesno_options = [
	'yes' => elgg_echo('option:yes'),
	'no' => elgg_echo('option:no')
];
$noyes_options = array_reverse($yesno_options);

$separator_options = [
	'comma' => elgg_echo('search_advanced:settings:multi_tag_separator:comma'),
	'space' => elgg_echo('search_advanced:settings:multi_tag_separator:space'),
];

$combine_search_results_options = [
	'no' => elgg_echo('option:no'),
	'objects' => elgg_echo('search_advanced:settings:combine_search_results:objects'),
	'all' => elgg_echo('search_advanced:settings:combine_search_results:all'),
];

echo elgg_format_element('label', [], elgg_echo('search_advanced:settings:filter_position'));
echo elgg_view('input/dropdown', [
	'name' => 'params[filter_position]',
	'options_values' => [
		'content' => elgg_echo('search_advanced:settings:filter_position:content'),
		'sidebar' => elgg_echo('search_advanced:settings:filter_position:sidebar'),
	],
	'value' => $plugin->filter_position,
	'class' => 'mls'
]);
echo elgg_format_element('div', ['class'=> 'elgg-subtext'], elgg_echo('search_advanced:settings:filter_position:info'));

echo elgg_format_element('label', [], elgg_echo('search_advanced:settings:search_types_menu_format'));
echo elgg_view('input/dropdown', [
	'name' => 'params[search_types_menu_format]',
	'options_values' => [
		'menu' => elgg_echo('search_advanced:settings:search_types_menu_format:menu'),
		'dropdown' => elgg_echo('search_advanced:settings:search_types_menu_format:dropdown'),
	],
	'value' => $plugin->search_types_menu_format,
	'class' => 'mls'
]);
echo elgg_format_element('div', ['class'=> 'elgg-subtext'], elgg_echo('search_advanced:settings:search_types_menu_format:info'));

echo elgg_format_element('label', [], elgg_echo('search_advanced:settings:combine_search_results'));
echo elgg_view('input/dropdown', [
	'name' => 'params[combine_search_results]',
	'options_values' => $combine_search_results_options,
	'value' => $plugin->combine_search_results,
	'class' => 'mls'
]);
echo elgg_format_element('div', ['class'=> 'elgg-subtext'], elgg_echo('search_advanced:settings:combine_search_results:info'));

echo elgg_format_element('label', [], elgg_echo('search_advanced:settings:search_with_loader'));
echo elgg_view('input/dropdown', [
		'name' => 'params[search_with_loader]',
		'options_values' => $noyes_options,
		'value' => $plugin->search_with_loader,
		'class' => 'mls'
		]);
echo elgg_format_element('div', ['class'=> 'elgg-subtext'], elgg_echo('search_advanced:settings:search_with_loader:info'));

// search hooks settings
echo elgg_format_element('label', [], elgg_echo('search_advanced:settings:search_hooks_enabled'));
echo elgg_view('input/dropdown', [
		'name' => 'params[search_hooks_enabled]',
		'options_values' => $yesno_options,
		'value' => $plugin->search_hooks_enabled,
		'class' => 'mls'
		]);
echo elgg_format_element('div', ['class'=> 'elgg-subtext'], elgg_echo('search_advanced:settings:search_hooks_enabled:info'));

echo '<div class="mls plm mbm elgg-divide-left">';
echo elgg_format_element('label', [], elgg_echo('search_advanced:settings:enable_multi_tag'));
echo elgg_view('input/dropdown', [
	'name' => 'params[enable_multi_tag]',
	'options_values' => $noyes_options,
	'value' => $plugin->enable_multi_tag,
	'class' => 'mls'
]);
echo elgg_format_element('div', ['class'=> 'elgg-subtext'], elgg_echo('search_advanced:settings:enable_multi_tag:info'));

echo elgg_format_element('label', [], elgg_echo('search_advanced:settings:multi_tag_separator'));
echo elgg_view('input/dropdown', [
	'name' => 'params[multi_tag_separator]',
	'options_values' => $separator_options,
	'value' => $plugin->multi_tag_separator,
	'class' => 'mls'
]);
echo elgg_format_element('div', ['class'=> 'elgg-subtext'], elgg_echo('search_advanced:settings:multi_tag_separator:info'));
echo '</div>';

$body = '';

$body .= elgg_format_element('div',['class' => 'elgg-admin-notices'], elgg_autop(elgg_echo('search_advanced:settings:profile_fields:disclaimer')));

if (elgg_is_active_plugin('groups')) {
	elgg_require_js('search_advanced/settings');
	$tabs = array(
		'tabs' => array(
			array(
				'text' => elgg_echo('search_advanced:settings:profile_fields:user'),
				'href' => '#',
				'selected' => true
			),
			array(
				'text' => elgg_echo('search_advanced:settings:profile_fields:group'),
				'href' => '#',
			),
		),
		'class' => 'search-advanced-settings-tabs'
	);

	$body .= elgg_view('navigation/tabs', $tabs);
}

$body .= elgg_format_element('div', ['class'=> 'search-advanced-settings-profile-fields'], elgg_view('search_advanced/settings/user_profile_fields', $vars));

if (elgg_is_active_plugin('groups')) {
	$body .= elgg_format_element('div', ['class'=> 'search-advanced-settings-profile-fields hidden'], elgg_view('search_advanced/settings/group_profile_fields', $vars));
}

echo elgg_view_module('inline', elgg_echo('search_advanced:settings:profile_fields'), $body);

<?php

$plugin = elgg_extract('entity', $vars);

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
	'#type' => 'checkbox',
	'#label' => elgg_echo('search_advanced:settings:query_required'),
	'#help' => elgg_echo('search_advanced:settings:query_required:help'),
	'name' => 'params[query_required]',
	'checked' => $plugin->query_required === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('search_advanced:settings:search_with_loader'),
	'#help' => elgg_echo('search_advanced:settings:search_with_loader:info'),
	'name' => 'params[search_with_loader]',
	'checked' => $plugin->search_with_loader === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

// search hooks settings
echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('search_advanced:settings:search_hooks_enabled'),
	'#help' => elgg_echo('search_advanced:settings:search_hooks_enabled:info'),
	'name' => 'params[search_hooks_enabled]',
	'checked' => $plugin->search_hooks_enabled === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

echo '<div class="mls plm mbm elgg-divide-left">';

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('search_advanced:settings:enable_multi_tag'),
	'#help' => elgg_echo('search_advanced:settings:enable_multi_tag:info'),
	'name' => 'params[enable_multi_tag]',
	'checked' => $plugin->enable_multi_tag === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
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

$body .= elgg_view_message('notice', elgg_echo('search_advanced:settings:profile_fields:disclaimer'), ['title' => false]);

$user_profile_fields = elgg_view('search_advanced/settings/user_profile_fields', $vars);

if (!elgg_is_active_plugin('groups')) {
	$body .= $user_profile_fields;
} else {
	$body .= elgg_view('page/components/tabs', [
		'tabs' => [
			[
				'text' => elgg_echo('search_advanced:settings:profile_fields:user'),
				'content' => $user_profile_fields,
				'selected' => true,
			],
			[
				'text' => elgg_echo('search_advanced:settings:profile_fields:group'),
				'content' => elgg_view('search_advanced/settings/group_profile_fields', $vars),
			],
		],
	]);
}

echo elgg_view_module('info', elgg_echo('search_advanced:settings:profile_fields'), $body);

<?php

$plugin = elgg_extract('entity', $vars);

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

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('search_advanced:settings:enable_search_type_selection'),
	'#help' => elgg_echo('search_advanced:settings:enable_search_type_selection:info'),
	'name' => 'params[enable_search_type_selection]',
	'checked' => $plugin->enable_search_type_selection === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('search_advanced:settings:enable_autocomplete'),
	'#help' => elgg_echo('search_advanced:settings:enable_autocomplete:info'),
	'name' => 'params[enable_autocomplete]',
	'checked' => $plugin->enable_autocomplete === 'yes',
	'switch' => true,
	'default' => 'no',
	'value' => 'yes',
]);

echo elgg_view_field([
	'#type' => 'fieldset',
	'class' => ['elgg-divide-left', 'mlm', 'plm'],
	'fields' => [
		[
			'#type' => 'checkbox',
			'#label' => elgg_echo('search_advanced:settings:enable_autocomplete_content'),
			'#help' => elgg_echo('search_advanced:settings:enable_autocomplete_content:info'),
			'name' => 'params[enable_autocomplete_content]',
			'checked' => $plugin->enable_autocomplete_content === 'yes',
			'switch' => true,
			'default' => 'no',
			'value' => 'yes',
		],
		[
			'#type' => 'checkbox',
			'#label' => elgg_echo('search_advanced:settings:enable_autocomplete_helpers'),
			'#help' => elgg_echo('search_advanced:settings:enable_autocomplete_helpers:info'),
			'name' => 'params[enable_autocomplete_helpers]',
			'checked' => $plugin->enable_autocomplete_helpers === 'yes',
			'switch' => true,
			'default' => 'no',
			'value' => 'yes',
		],
	],
]);

$body = '';

$body .= elgg_view_message('notice', elgg_echo('search_advanced:settings:profile_fields:disclaimer'), ['title' => false]);

$user_profile_fields = elgg_view('plugins/search_advanced/settings/user_profile_fields', $vars);

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
				'content' => elgg_view('plugins/search_advanced/settings/group_profile_fields', $vars),
			],
		],
	]);
}

echo elgg_view_module('info', elgg_echo('search_advanced:settings:profile_fields'), $body);

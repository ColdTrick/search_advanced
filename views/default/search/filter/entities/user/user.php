<?php

$search_filter = elgg_extract('search_filter', $vars, get_input('filter', []));
$profile_fields = elgg()->fields->get('user', 'user');
$profile_field_values = elgg_get_plugin_setting('user_profile_fields_search_form', 'search_advanced');

if (empty($profile_field_values) || empty($profile_fields)) {
	return;
}

$submit_values = elgg_extract('profile_fields', $search_filter, []);
$profile_field_values = json_decode($profile_field_values, true);

$show_placeholder = (bool) elgg_extract('show_placeholder', $vars, true);
$show_label = (bool) elgg_extract('show_label', $vars, false);

$body = '';
foreach ($profile_fields as $field) {
	$profile_field = elgg_extract('name', $field);
	if (!in_array($profile_field, $profile_field_values)) {
		continue;
	}
	
	$body .= elgg_view_field([
		'#type' => 'search',
		'#label' => $show_label ? elgg_extract('#label', $field) : null,
		'name' => "filter[profile_fields][{$profile_field}]",
		'value' => elgg_extract($profile_field, $submit_values),
		'placeholder' => $show_placeholder ? elgg_extract('#label', $field) : null,
	]);
}

if (empty($body)) {
	return;
}

if (elgg_extract('show_button', $vars, false)) {
	$body .= elgg_view_field([
		'#type' => 'submit',
		'text' => elgg_echo('search'),
		'icon' => 'search',
	]);
}

$module_type = elgg_extract('module_type', $vars, 'aside');

if (empty($module_type)) {
	echo $body;
	return;
}

echo elgg_view_module($module_type, elgg_echo('search:filter:entities:user:title'), $body);

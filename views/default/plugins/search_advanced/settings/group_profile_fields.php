<?php

$profile_fields = elgg()->fields->get('group', 'group');

$profile_field_metadata_search_values = elgg_get_plugin_setting('group_profile_fields_metadata_search', 'search_advanced', []);
if (!empty($profile_field_metadata_search_values)) {
	$profile_field_metadata_search_values = json_decode($profile_field_metadata_search_values, true);
}

if (empty($profile_field_metadata_search_values)) {
	$profile_field_metadata_search_values = [];
}

echo "<table class='elgg-table'>";
echo '<thead><tr>';
echo '<th>' . elgg_echo('search_advanced:settings:profile_fields:field') . '</th>';
echo "<th class='center'>" . elgg_echo('search_advanced:settings:profile_fields:metadata_search');
echo elgg_view('input/hidden', [
	'name' => 'params[group_profile_fields_metadata_search]',
	'value' => 0,
]) . '</th>';
echo '</tr></thead>';

foreach ($profile_fields as $field) {
	$type = elgg_extract('#type', $field);
	if ($type === 'hidden') {
		continue;
	}
	
	$metadata_name = elgg_extract('name', $field);
	
	$label = elgg_extract('#label', $field) . " ({$type})";

	echo '<tr>';
	echo '<td><label>' . $label . '</label></td>';
	echo "<td class='center'>" . elgg_view('input/checkbox', [
		'name' => 'params[group_profile_fields_metadata_search][]',
		'value' => $metadata_name,
		'checked' => in_array($metadata_name, $profile_field_metadata_search_values),
		'default' => false,
	]) . '</td>';
	echo '</tr>';
}

echo '</table>';

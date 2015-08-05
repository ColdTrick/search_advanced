<?php

$profile_fields = elgg_get_config('profile_fields');

$profile_field_values_search_form = elgg_get_plugin_setting('user_profile_fields_search_form', 'search_advanced', []);
if (!empty($profile_field_values_search_form)) {
	$profile_field_values_search_form = json_decode($profile_field_values_search_form, true);
}

$profile_field_search_form_soundex_values = elgg_get_plugin_setting('user_profile_fields_search_form_soundex', 'search_advanced', []);
if (!empty($profile_field_search_form_soundex_values)) {
	$profile_field_search_form_soundex_values = json_decode($profile_field_search_form_soundex_values, true);
}

$profile_field_metadata_search_values = elgg_get_plugin_setting('user_profile_fields_metadata_search', 'search_advanced', []);
if (!empty($profile_field_metadata_search_values)) {
	$profile_field_metadata_search_values = json_decode($profile_field_metadata_search_values, true);
}

echo "<table class='elgg-table'>";
echo "<tr>";
echo "<th>" . elgg_echo("search_advanced:settings:profile_fields:field") . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:user_profile_fields:show_on_form") . "*";
echo elgg_view("input/hidden", array("name" => "params[user_profile_fields]", "value" => 0)) . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:user_profile_fields:use_soundex") . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:profile_fields:metadata_search") . "</th>";
echo "</tr>";

foreach ($profile_fields as $metadata_name => $type) {
	
	if (elgg_language_key_exists("profile:{$metadata_name}")) {
		$name = elgg_echo("profile:{$metadata_name}");
	} else {
		$name = $metadata_name;
	}

	$name .= " ({$type})";

	$show_field_options = array(
		'name' => 'params[user_profile_fields_search_form][]',
		'value' => $metadata_name,
		'checked' => in_array($metadata_name, $profile_field_values_search_form)
	);
	
	$soundex_field_options = array(
		'name' => 'params[user_profile_fields_search_form_soundex][]',
		'value' => $metadata_name,
		'checked' => in_array($metadata_name, $profile_field_search_form_soundex_values)
	);
		
	$metadata_search_field_options = array(
		'name' => 'params[user_profile_fields_metadata_search][]',
		'value' => $metadata_name,
		'checked' => in_array($metadata_name, $profile_field_metadata_search_values)
	);

	echo "<tr>";
	echo "<td><label>" . $name . "</label></td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $show_field_options) . "</td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $soundex_field_options) . "</td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $metadata_search_field_options) . "</td>";
	echo "</tr>";
}

echo "</table>";

echo elgg_format_element('div', ['class' => 'elgg-subtext'], '* ' . elgg_echo('search_advanced:settings:user_profile_fields:info'));

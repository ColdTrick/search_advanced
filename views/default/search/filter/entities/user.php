<?php

$profile_fields = elgg_get_config("profile_fields");
$profile_field_values = elgg_get_plugin_setting("user_profile_fields_search_form", "search_advanced");
$profile_field_soundex_values = elgg_get_plugin_setting("user_profile_fields_search_form_soundex", "search_advanced");
if (empty($profile_field_values) || empty($profile_fields)) {
	return;
}

$submit_values = (array) get_input("search_advanced_profile_fields");
$profile_field_values = json_decode($profile_field_values, true);
$profile_field_soundex_submit_values = (array) get_input("search_advanced_profile_fields_soundex");
$profile_field_soundex_values = json_decode($profile_field_soundex_values, true);

$show_placeholder_default = false;
$show_label_default = true;
if (elgg_extract('filter_position', $vars) === 'sidebar') {
	$show_placeholder_default = true;
	$show_label_default = false;
}
$show_placeholder = (bool) elgg_extract('show_placeholder', $vars, $show_placeholder_default);
$show_label = (bool) elgg_extract('show_label', $vars, $show_label_default);

$output = array();
foreach ($profile_fields as $profile_field => $field_type) {
	if (!in_array($profile_field, $profile_field_values)) {
		continue;
	}
	
	$name = $profile_field;
	if (elgg_language_key_exists("profile:{$profile_field}")) {
		$name = elgg_echo("profile:{$profile_field}");
	}

	$row = new stdClass();
	$row->label = $name;
	$row->input = elgg_view("input/text", array(
		"name" => "search_advanced_profile_fields[" . $profile_field . "]",
		"value" => elgg_extract($profile_field, $submit_values),
		"placeholder" => $show_placeholder ? $name : '',
	));
	if (in_array($profile_field, $profile_field_soundex_values)) {
		$soundex_options = array(
			"name" => "search_advanced_profile_fields_soundex[]",
			"value" => $profile_field,
			"label" => elgg_echo("search_advanced:users:profile_field:soundex")
		);
		if (in_array($profile_field, $profile_field_soundex_submit_values)) {
			$soundex_options["checked"] = "checked";
		}
			
		$row->soundex = elgg_view("input/checkbox", $soundex_options);
	}

	$output[] = $row;
}

if (empty($output)) {
	return;
}

$show_button = (bool) elgg_extract('show_button', $vars, true);
$soundex_newline = (bool) elgg_extract('soundex_newline', $vars, elgg_in_context('widgets'));

foreach ($output as $row) {
	$result = '';
	
	if ($show_label) {
		$result .= elgg_format_element('label', [], $row->label);
	}
	
	$result .= $row->input;
	
	if ($row->soundex) {
		if ($soundex_newline) {
			$result .= '<br />';
		}

		$result .= $row->soundex;
	}
		
	echo elgg_format_element('div', [], $result);
}

if ($show_button) {
	echo elgg_format_element('div', [], elgg_view("input/submit", ["value" => elgg_echo("search")]));
}

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

$show_placeholder = (bool) elgg_extract('show_placeholder', $vars, false);

$output = array();
foreach ($profile_field_values as $profile_field) {
	if (!isset($profile_fields[$profile_field])) {
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
$show_label = (bool) elgg_extract('show_label', $vars, true);
$soundex_newline = (bool) elgg_extract('soundex_newline', $vars, elgg_in_context('widgets'));

$table_rows = [];
foreach ($output as $row) {
	
	$cells = [];
	
	if ($show_label) {
		$cells[] = elgg_format_element('label', [], $row->label);
	}
	
	if ($soundex_newline) {
		$cell = $row->input;
		
		if ($row->soundex) {
			$cell .= "<br />" . $row->soundex;
		}
		
		$cells[] = $cell;
	} else {
		$cells[] = $row->input;
		
		if ($row->soundex) {
			$cells[] = $row->soundex;
		} else {
			$cells[] = "&nbsp;";
		}
		
	}
	
	$table_rows[] = '<td>' . implode('</td><td>', $cells) . '</td>';
}

echo elgg_format_element('table', ['class' => 'search-advanced-user-profile-table mtm'], '<tr>' . implode('</tr><tr>', $table_rows) . '</tr>');

if ($show_button) {
	echo elgg_view("input/submit", array("value" => elgg_echo("search")));
}

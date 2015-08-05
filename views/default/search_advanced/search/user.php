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
		"value" => elgg_extract($profile_field, $submit_values)
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

if (!empty($output)) {
	echo "<table class='search-advanced-user-profile-table mtm'>";
	foreach ($output as $row) {
		echo "<tr>";
		echo "<td><label>" . $row->label . "</label></td>";
		if (elgg_in_context("widgets")) {
			echo "<td>" . $row->input;
			if ($row->soundex) {
				echo "<br />" . $row->soundex;
			}
			echo "</td>";
		} else {
			echo "<td>" . $row->input . "</td><td>";
			if ($row->soundex) {
				echo $row->soundex;
			} else {
				echo "&nbsp;";
			}
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	echo elgg_view("input/submit", array("value" => elgg_echo("search")));
}
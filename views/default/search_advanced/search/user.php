<?php

$profile_fields = elgg_get_config("profile_fields");
$profile_field_values = elgg_get_plugin_setting("user_profile_fields_search_form", "search_advanced");

if (empty($profile_field_values) || empty($profile_fields)) {
	return;
}

$submit_values = (array) get_input("search_advanced_profile_fields");
$profile_field_values = json_decode($profile_field_values, true);
$profile_field_soundex_submit_values = (array) get_input("search_advanced_profile_fields_soundex");
$output = array();

foreach ($profile_field_values as $profile_field) {
	if (!isset($profile_fields[$profile_field])) {
		continue;
	}

	$lan_key = "profile:" . $profile_field;
	$name = $profile_field;
	if (elgg_echo($lan_key) !== $lan_key) {
		$name = elgg_echo($lan_key);
	}

	$row = new stdClass();
	$row->label = $name;
	$row->input = elgg_view("input/text", array(
		"name" => "search_advanced_profile_fields[" . $profile_field . "]",
		"value" => elgg_extract($profile_field, $submit_values)
	));
	
	$output[] = $row;
}

if (!empty($output)) {
	echo "<table class='search-advanced-user-profile-table mtm'>";
	foreach ($output as $row) {
		echo "<tr>";
		echo "<td><label>" . $row->label . "</label></td>";
		if (elgg_in_context("widgets")) {
			echo "<td>" . $row->input;
			
			echo "</td>";
		} else {
			echo "<td>" . $row->input . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	echo elgg_view("input/submit", array("value" => elgg_echo("search")));
}
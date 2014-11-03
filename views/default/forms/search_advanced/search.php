<?php

if ($vars["search_type"]) {
	echo elgg_view("input/hidden", array("name" => "search_type", "value" => $vars["search_type"]));
}

if ($vars["type"]) {
	echo elgg_view("input/hidden", array("name" => "entity_type", "value" => $vars["type"]));
}

if ($vars["subtype"]) {
	echo elgg_view("input/hidden", array("name" => "entity_subtype", "value" => $vars["subtype"]));
}

if ($vars["container_guid"]) {
	echo elgg_view("input/hidden", array("name" => "container_guid", "value" => $vars["container_guid"]));
}

echo elgg_view("input/text", array("name" => "q", "value" => $vars["query"] , "class" => "ui-front"));
echo elgg_view("input/submit", array("value" => elgg_echo("submit"), "class" => "hidden"));

if (($user = elgg_get_logged_in_user_entity()) && elgg_trigger_plugin_hook("search_multisite", "search", array("user" => $user), false)) {
	$current_value = 0;
	if (!empty($_SESSION["search_advanced:multisite"])) {
		$current_value = $_SESSION["search_advanced:multisite"];
	}

	echo "<div class='float-alt'>";
	echo elgg_echo("search_advanced:multisite:label") . " ";
	echo elgg_view("input/dropdown", array("name" => "multisite", "value" => $current_value, "options_values" => array(0 => elgg_echo("search_advanced:multisite:current"), 1 => elgg_echo("search_advanced:multisite:mine"))));
	echo "</div>";
}

if (elgg_extract("type", $vars, false) === "user") {
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
		
		$lan_key = "profile:" . $profile_field;
		$name = $profile_field;
		if (elgg_echo($lan_key) !== $lan_key) {
			$name = elgg_echo($lan_key);
		}
		
		$row = array();
		$row[] = $name;
		$row[] = elgg_view("input/text", array(
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
			
			$row[] = elgg_view("input/checkbox", $soundex_options);
		} else {
			$row[] = "&nbsp;";
		}
		
		$output[] = $row;
	}
	
	if (!empty($output)) {
		echo "<table class='search-advanced-user-profile-table mtm'>";
		foreach ($output as $row) {
			echo "<tr>";
			echo "<td><label>" . $row[0] . "</label></td>";
			echo "<td>" . $row[1] . "</td>";
			echo "<td>" . $row[2] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo elgg_view("input/submit", array("value" => elgg_echo("search")));
	}
}
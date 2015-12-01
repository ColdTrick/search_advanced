<?php

$plugin = elgg_extract("entity", $vars);

$profile_fields = elgg_get_config("profile_fields");

$profile_field_values_search_form = $plugin->user_profile_fields_search_form;
if (!empty($profile_field_values_search_form)) {
	$profile_field_values_search_form = json_decode($profile_field_values_search_form, true);
} else {
	$profile_field_values_search_form = array();
}

$user_profile_fields_search_form_autocomplete_values = $plugin->user_profile_fields_search_form_autocomplete;
if (!empty($user_profile_fields_search_form_autocomplete_values)) {
	$user_profile_fields_search_form_autocomplete_values = json_decode($user_profile_fields_search_form_autocomplete_values, true);
} else {
	$user_profile_fields_search_form_autocomplete_values = array();
}

// $profile_field_search_form_soundex_values = $plugin->user_profile_fields_search_form_soundex;
// if (!empty($profile_field_search_form_soundex_values)) {
// 	$profile_field_search_form_soundex_values = json_decode($profile_field_search_form_soundex_values, true);
// } else {
// 	$profile_field_search_form_soundex_values = array();
// }

// $profile_field_metadata_search_values = $plugin->user_profile_fields_metadata_search;
// if (!empty($profile_field_metadata_search_values)) {
// 	$profile_field_metadata_search_values = json_decode($profile_field_metadata_search_values, true);
// } else {
// 	$profile_field_metadata_search_values = array();
// }

echo "<table class='elgg-table'>";
echo "<tr>";
echo "<th>" . elgg_echo("search_advanced:settings:profile_fields:field") . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:user_profile_fields:show_on_form") . "*";
echo elgg_view("input/hidden", array("name" => "params[user_profile_fields]", "value" => 0)) . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:user_profile_fields:use_autocomplete") . "</th>";
// echo "<th class='center'>" . elgg_echo("search_advanced:settings:user_profile_fields:use_soundex") . "</th>";
// echo "<th class='center'>" . elgg_echo("search_advanced:settings:profile_fields:metadata_search") . "</th>";
echo "</tr>";
foreach ($profile_fields as $metadata_name => $type) {
	$lan_key = "profile:" . $metadata_name;
	$name = $metadata_name;
	if (elgg_echo($lan_key) !== $lan_key) {
		$name = elgg_echo($lan_key);
	}

	$name .= " (" . $type . ")";

	$show_field_options = array(
		"name" => "params[user_profile_fields_search_form][]",
		"value" => $metadata_name
	);
	if (in_array($metadata_name, $profile_field_values_search_form)) {
		$show_field_options["checked"] = "checked";
	}

	$use_autocomplete_options = array(
		"name" => "params[user_profile_fields_search_form_autocomplete][]",
		"value" => $metadata_name
	);
	if (in_array($metadata_name, $user_profile_fields_search_form_autocomplete_values)) {
		$use_autocomplete_options["checked"] = "checked";
	}
	
// 	$soundex_field_options = array(
// 		"name" => "params[user_profile_fields_search_form_soundex][]",
// 		"value" => $metadata_name
// 	);
// 	if (in_array($metadata_name, $profile_field_search_form_soundex_values)) {
// 		$soundex_field_options["checked"] = "checked";
// 	}
	
// 	$metadata_search_field_options = array(
// 		"name" => "params[user_profile_fields_metadata_search][]",
// 		"value" => $metadata_name
// 	);
// 	if (in_array($metadata_name, $profile_field_metadata_search_values)) {
// 		$metadata_search_field_options["checked"] = "checked";
// 	}

	echo "<tr>";
	echo "<td><label>" . $name . "</label></td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $show_field_options) . "</td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $use_autocomplete_options) . "</td>";
// 	echo "<td class='center'>" . elgg_view("input/checkbox", $soundex_field_options) . "</td>";
// 	echo "<td class='center'>" . elgg_view("input/checkbox", $metadata_search_field_options) . "</td>";
	echo "</tr>";

}
echo "</table>";

echo "<div class='elgg-subtext'>* " . elgg_echo('search_advanced:settings:user_profile_fields:info') . "</div>";
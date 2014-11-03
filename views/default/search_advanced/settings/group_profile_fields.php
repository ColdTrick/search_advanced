<?php

$plugin = elgg_extract("entity", $vars);

$profile_fields = elgg_get_config("group");

$profile_field_metadata_search_values = $plugin->group_profile_fields_metadata_search;
if (!empty($profile_field_metadata_search_values)) {
	$profile_field_metadata_search_values = json_decode($profile_field_metadata_search_values, true);
} else {
	$profile_field_metadata_search_values = array();
}

echo "<table class='elgg-table'>";
echo "<tr>";
echo "<th>" . elgg_echo("search_advanced:settings:profile_fields:field") . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:profile_fields:metadata_search") . "</th>";
echo "</tr>";
foreach ($profile_fields as $metadata_name => $type) {
	$lan_key = "group:" . $metadata_name;
	$name = $metadata_name;
	if (elgg_echo($lan_key) !== $lan_key) {
		$name = elgg_echo($lan_key);
	}

	$name .= " (" . $type . ")";

	$metadata_search_field_options = array(
		"name" => "params[group_profile_fields_metadata_search][]",
		"value" => $metadata_name
	);
	if (in_array($metadata_name, $profile_field_metadata_search_values)) {
		$metadata_search_field_options["checked"] = "checked";
	}

	echo "<tr>";
	echo "<td><label>" . $name . "</label></td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $metadata_search_field_options) . "</td>";
	echo "</tr>";

}
echo "</table>";

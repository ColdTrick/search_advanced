<?php

$plugin = elgg_extract("entity", $vars);

$yesno_options = array(
	"yes" => elgg_echo("option:yes"),
	"no" => elgg_echo("option:no")
);
$noyes_options = array_reverse($yesno_options);

$separator_options = array(
	"comma" => elgg_echo("search_advanced:settings:multi_tag_separator:comma"),
	"space" => elgg_echo("search_advanced:settings:multi_tag_separator:space"),
);

$profile_fields = elgg_get_config("profile_fields");
$profile_field_values = $plugin->user_profile_fields;
if (!empty($profile_field_values)) {
	$profile_field_values = json_decode($profile_field_values, true);
}
$profile_field_soundex_values = $plugin->user_profile_fields_soundex;
if (!empty($profile_field_soundex_values)) {
	$profile_field_soundex_values = json_decode($profile_field_soundex_values, true);
}

echo "<label>" . elgg_echo('search_advanced:settings:combine_search_results') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[combine_search_results]", "options_values" => $noyes_options, "value" => $plugin->combine_search_results));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:combine_search_results:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:enable_multi_tag') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[enable_multi_tag]", "options_values" => $noyes_options, "value" => $plugin->enable_multi_tag));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:enable_multi_tag:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:multi_tag_separator') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[multi_tag_separator]", "options_values" => $separator_options, "value" => $plugin->multi_tag_separator));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:multi_tag_separator:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:user_profile_fields') . "</label> ";
echo "<table class='elgg-table-alt'>";
echo "<tr>";
echo "<th>" . elgg_echo("search_advanced:settings:user_profile_fields:field") . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:user_profile_fields:show_on_form");
echo elgg_view("input/hidden", array("name" => "params[user_profile_fields]", "value" => 0)) . "</th>";
echo "<th class='center'>" . elgg_echo("search_advanced:settings:user_profile_fields:use_soundex") . "</th>";
echo "</tr>";
foreach ($profile_fields as $metadata_name => $type) {
	$lan_key = "profile:" . $metadata_name;
	$name = $metadata_name;
	if (elgg_echo($lan_key) !== $lan_key) {
		$name = elgg_echo($lan_key);
	}
	
	$name .= " (" . $type . ")";
	
	$show_field_options = array(
		"name" => "params[user_profile_fields][]",
		"value" => $metadata_name
	);
	if (in_array($metadata_name, $profile_field_values)) {
		$show_field_options["checked"] = "checked";
	}
	$soundex_field_options = array(
		"name" => "params[user_profile_fields_soundex][]",
		"value" => $metadata_name
	);
	if (in_array($metadata_name, $profile_field_soundex_values)) {
		$soundex_field_options["checked"] = "checked";
	}
	
	echo "<tr>";
	echo "<td><label>" . $name . "</label></td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $show_field_options) . "</td>";
	echo "<td class='center'>" . elgg_view("input/checkbox", $soundex_field_options) . "</td>";
	echo "</tr>";
	
}
echo "</table>";

echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:user_profile_fields:info') . "</div>";

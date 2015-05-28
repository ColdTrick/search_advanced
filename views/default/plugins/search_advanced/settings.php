<?php
$yesno_options = array(
	"yes" => elgg_echo("option:yes"),
	"no" => elgg_echo("option:no")
);
$noyes_options = array_reverse($yesno_options);

echo "<label>" . elgg_echo('search_advanced:settings:combine_search_results') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[combine_search_results]", "options_values" => $noyes_options, "value" => $vars['entity']->combine_search_results));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:combine_search_results:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:enable_multi_tag') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[enable_multi_tag]", "options_values" => $noyes_options, "value" => $vars['entity']->enable_multi_tag));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:enable_multi_tag:info') . "</div>";

$title = elgg_echo('search_advanced:settings:profile_fields');
$body = "";

$body .= "<div class='search-advanced-settings-profile-fields'>";
$body .= elgg_view("search_advanced/settings/user_profile_fields", $vars);
$body .= "</div>";

echo elgg_view_module("inline", $title, $body);

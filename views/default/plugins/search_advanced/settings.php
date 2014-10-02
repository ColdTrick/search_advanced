<?php
$yesno_options = array(
	"yes" => elgg_echo("option:yes"),
	"no" => elgg_echo("option:no")
);
$noyes_options = array_reverse($yesno_options);

$separator_options = array(
	"comma" => elgg_echo("search_advanced:settings:multi_tag_separator:comma"),
	"space" => elgg_echo("search_advanced:settings:multi_tag_separator:space"),
);

echo "<label>" . elgg_echo('search_advanced:settings:combine_search_results') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[combine_search_results]", "options_values" => $noyes_options, "value" => $vars['entity']->combine_search_results));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:combine_search_results:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:enable_multi_tag') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[enable_multi_tag]", "options_values" => $noyes_options, "value" => $vars['entity']->enable_multi_tag));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:enable_multi_tag:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:multi_tag_separator') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[multi_tag_separator]", "options_values" => $separator_options, "value" => $vars['entity']->multi_tag_separator));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:multi_tag_separator:info') . "</div>";

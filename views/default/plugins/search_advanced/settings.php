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

echo "<label>" . elgg_echo('search_advanced:settings:combine_search_results') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[combine_search_results]", "options_values" => $noyes_options, "value" => $plugin->combine_search_results));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:combine_search_results:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:enable_multi_tag') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[enable_multi_tag]", "options_values" => $noyes_options, "value" => $plugin->enable_multi_tag));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:enable_multi_tag:info') . "</div>";

echo "<label>" . elgg_echo('search_advanced:settings:multi_tag_separator') . "</label> ";
echo elgg_view("input/dropdown", array("name" => "params[multi_tag_separator]", "options_values" => $separator_options, "value" => $plugin->multi_tag_separator));
echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:multi_tag_separator:info') . "</div>";

$title = elgg_echo('search_advanced:settings:profile_fields');
$body = "";

if (elgg_is_active_plugin("groups")) {
	$tabs = array( 
		"tabs" => array(
			array(
				"text" => elgg_echo("search_advanced:settings:profile_fields:user"),
				"href" => "#",
				"selected" => true
			),
			array(
				"text" => elgg_echo("search_advanced:settings:profile_fields:group"),
				"href" => "#",
			),
		),
		"class" => "search-advanced-settings-tabs"	
	);

	$body .= elgg_view("navigation/tabs", $tabs);
}

$body .= "<div class='search-advanced-settings-profile-fields'>";
$body .= elgg_view("search_advanced/settings/user_profile_fields", $vars);
$body .= "</div>";

if (elgg_is_active_plugin("groups")) {
	$body .= "<div class='search-advanced-settings-profile-fields hidden'>";
	$body .= elgg_view("search_advanced/settings/group_profile_fields", $vars);
	$body .= "</div>";
}

echo elgg_view_module("inline", $title, $body);

?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".search-advanced-settings-tabs li").click(function() {
			if (!$(this).hasClass("elgg-state-selected")) {
				$(".search-advanced-settings-tabs li").toggleClass("elgg-state-selected");
				$(".search-advanced-settings-profile-fields").toggleClass("hidden");
			}
		});
	});
</script>
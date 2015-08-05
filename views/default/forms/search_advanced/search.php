<?php

$search_type = elgg_extract('search_type', $vars);
$type = elgg_extract('type', $vars);
$subtype = elgg_extract('subtype', $vars);
$container_guid = (int) elgg_extract('container_guid', $vars);
$query = elgg_extract('query', $vars);

if ($search_type) {
	echo elgg_view("input/hidden", ["name" => "search_type", "value" => $search_type]);
}

if ($type) {
	echo elgg_view("input/hidden", ["name" => "entity_type", "value" => $type]);
}

if ($subtype) {
	echo elgg_view("input/hidden", ["name" => "entity_subtype", "value" => $subtype]);
}

if ($container_guid) {
	echo elgg_view("input/hidden", ["name" => "container_guid", "value" => $container_guid]);
}

echo elgg_view("input/text", ["name" => "q", "value" => $query , "class" => "ui-front"]);
echo elgg_view("input/submit", ["value" => elgg_echo("submit"), "class" => "hidden"]);

if (($user = elgg_get_logged_in_user_entity()) && elgg_trigger_plugin_hook("search_multisite", "search", array("user" => $user), false)) {
	$current_value = 0;
	if (!empty($_SESSION["search_advanced:multisite"])) {
		$current_value = $_SESSION["search_advanced:multisite"];
	}

	echo "<div class='float-alt'>";
	echo elgg_echo("search_advanced:multisite:label") . " ";
	echo elgg_view("input/dropdown", [
		"name" => "multisite",
		"value" => $current_value,
		"options_values" => [
			0 => elgg_echo("search_advanced:multisite:current"),
			1 => elgg_echo("search_advanced:multisite:mine")
		]
	]);
	echo "</div>";
}

if ($type === 'user') {
	echo elgg_view('search_advanced/search/user', $vars);
}
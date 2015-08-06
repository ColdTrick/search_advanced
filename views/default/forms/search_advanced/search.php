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

echo elgg_view("input/text", ["name" => "q", "value" => $query , "class" => "ui-front mbs"]);
echo elgg_view("input/submit", ["value" => elgg_echo("submit"), "class" => "hidden"]);

if ($type === 'user') {
	echo elgg_view('search_advanced/search/user', $vars);
}
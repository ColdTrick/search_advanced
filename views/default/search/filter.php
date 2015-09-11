<?php

$params = elgg_extract('params', $vars, $vars);

$search_type = elgg_extract('search_type', $params);
$type = elgg_extract('type', $params);
$subtype = elgg_extract('subtype', $params);

$view_parts = [$search_type, $type, $subtype];
$view = rtrim(implode('/', $view_parts), '/');

if (empty($view)) {
	return;
}

$view = "search/filter/$view";

if (!elgg_view_exists($view)) {
	return;
}

// sometimes params contain stuff that should be in vars
$vars = array_merge($vars, $params);

$filter_position = elgg_extract('filter_position', $vars, 'content');

if ($filter_position !== elgg_get_plugin_setting('filter_position', 'search_advanced', 'content')) {
	return;
}

echo elgg_view($view, $vars);


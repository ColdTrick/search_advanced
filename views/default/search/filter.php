<?php

$params = elgg_extract('params', $vars, $vars);

$search_type = elgg_extract('search_type', $params, get_input('search_type', 'all'));
$type = elgg_extract('type', $params, get_input('entity_type'));
$subtype = elgg_extract('subtype', $params, get_input('entity_subtype'));

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

echo elgg_view($view, $vars);

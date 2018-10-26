<?php

$params = elgg_extract('params', $vars, $vars);

$search_type = elgg_extract('search_type', $params, get_input('search_type', 'all'));
$type = elgg_extract('type', $params, get_input('entity_type'));
$subtype = elgg_extract('subtype', $params, get_input('entity_subtype', $type));

// try different views

$views = [];

if (!elgg_is_empty($search_type)) {
	if (!elgg_is_empty($type)) {
		if (!elgg_is_empty($subtype)) {
			$views[] = "search/filter/{$search_type}/{$type}/{$subtype}";
		}
		$views[] = "search/filter/{$search_type}/{$type}/default";
	}
	$views[] = "search/filter/{$search_type}";
}

if (!elgg_is_empty($type)) {
	if (!elgg_is_empty($subtype)) {
		$views[] = "search/filter/{$type}/{$subtype}";
	}
	$views[] = "search/filter/{$type}/default";
}

$view = false;
foreach ($views as $check_view) {
	$check_view = trim($check_view, '/');
	if (!elgg_view_exists($check_view)) {
		continue;
	}
	
	$view = $check_view;
	break;
}

if (empty($view)) {
	return;
}

// sometimes params contain stuff that should be in vars
$vars = array_merge($vars, $params);

echo elgg_view($view, $vars);

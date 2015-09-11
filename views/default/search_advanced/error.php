<?php

$params = elgg_extract('params', $vars, []);
$query = elgg_extract('query', $params);
$search_filter = elgg_extract('search_filter', $params, []);

// check that we have an actual query
if ($query || !empty($search_filter)) {
	return;
}

$title = elgg_echo('search:search_error');
$vars['body'] = elgg_echo('search:no_query');
$vars['title'] = $title;

$result = elgg_view('search/layout', $vars);
if (!elgg_is_xhr()) {
	$result = elgg_view_page($title, $result);
}

echo $result;

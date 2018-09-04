<?php

$query = elgg_extract('query', $vars, '');
$service = elgg_extract('service', $vars);
$count = elgg_extract('count', $vars, 0);
$highlight_query = elgg_extract('highlight_query', $vars, true);

// highlight search terms
if (empty($query) || empty($count)) {
	echo elgg_echo('search_advanced:results:empty:title');
	return;
}
	
if ($highlight_query && $service) {
	$query = $service->getHighlighter()->highlightWords($query);
}
	
echo elgg_echo('search_advanced:results:title', [$count, $query]);

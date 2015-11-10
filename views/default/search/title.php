<?php

$query = elgg_extract('query', $vars, '');
$count = elgg_extract('count', $vars);
$highlight_query = elgg_extract('highlight_query', $vars, true);

// highlight search terms
if (!empty($query)) {
	if ($highlight_query) {
		$searched_words = search_remove_ignored_words($query, 'array');
		$query = search_highlight_words($searched_words, $query);
	}
	$query = "\"$query\"";
}

echo elgg_echo('search_advanced:results:title', [$count, $query]);

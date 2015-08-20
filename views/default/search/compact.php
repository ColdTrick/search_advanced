<?php

$entity = elgg_extract('entity', $vars);

if (!$entity) {
	return;
}

$title = $entity->getVolatileData('search_matched_title');

if (empty($title)) {
	$title = $entity->title;
	if ($title === null) {
		$title = $entity->name;
	}
}

if (empty($title)) {
	$title = elgg_extract('title', $vars, $title);
}

$url = $entity->getURL();

$link = elgg_view('output/url', [
	'text' => $title,
	'href' => $url
]);

echo elgg_format_element('p', [], $link);

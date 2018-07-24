<?php

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof \ElggEntity) {
	return;
}

$title = elgg_extract('title', $vars, $entity->getVolatileData('search_matched_title'));

if (empty($title)) {
	$title = $entity->getDisplayName();
}

$link = elgg_view('output/url', [
	'text' => $title,
	'href' => $entity->getURL(),
]);

echo elgg_format_element('p', [], $link);

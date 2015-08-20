<?php

$entity = $vars['entity'];
if (empty($entity) || !elgg_instanceof($entity, 'object', 'discussion_reply')) {
	return;
}

$topic = $entity->getContainerEntity();
if (empty($topic) || !elgg_instanceof($topic, 'object', 'groupforumtopic')) {
	return;
}

$vars['title'] = elgg_echo('discussion:search:title', [$topic->title]);

echo elgg_view('search/compact', $vars);


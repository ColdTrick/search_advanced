<?php
/**
 * Default search view for a comment
 *
 * @uses $vars['entity'] Comment returned in a search
 */

$entity = $vars['entity'];

$container = $entity->getContainerEntity();

if ($container->getType() == 'object') {
	$title = $container->title;
} else {
	$title = $container->name;
}

if (!$title) {
	$title = elgg_echo('item:' . $container->getType() . ':' . $container->getSubtype());
}

if (!$title) {
	$title = elgg_echo('item:' . $container->getType());
}

$vars['title'] = elgg_echo('search:comment_on', [$title]);

echo elgg_view('search/compact', $vars);

<?php
/** shows as floating tag for a searched entity when showing in the combined search listing */
if (!elgg_in_context('combined_search')) {
	return;
}

$search_params = elgg_extract('params', $vars);
$search_type = elgg_extract('search_type', $search_params);
if ($search_type !== 'entities') {
	return;
}

$entity = elgg_extract('entity', $vars);

$type = $entity->getType();
$subtype = $entity->getSubtype();

$href = search_advanced_get_search_url([
	'entity_type' => $type,
	'entity_subtype' => $subtype,
	'search_type' => $search_type
]);

echo elgg_view('output/url', [
	'class' => 'float-alt elgg-quiet',
	'href' => $href,
	'text' => elgg_echo(rtrim("item:{$type}:{$subtype}", ':'))
]);

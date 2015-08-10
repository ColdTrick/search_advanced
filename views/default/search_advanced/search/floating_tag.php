<?php
/** shows as floating tag for a searched entity when showing in the combined search listing */
if (elgg_get_plugin_setting("combine_search_results", "search_advanced") !== "yes") {
	return;
}

$entity = elgg_extract('entity', $vars);

if (get_input('search_type') !== 'all') {
	// can't use params['search_type'] as that is changed to entities
	return;
}

$type = $entity->getType();
if ($type !== 'object') {
	return;
}

$subtype = $entity->getSubtype();
$url_options = [
	"class" => "float-alt elgg-quiet",
	"href" => "search?q=" . $vars['params']['query'] . "&entity_subtype=" . $subtype . "&entity_type=" . $type . "&search_type=entities",
	"text" => elgg_echo("item:" . $type . ":" . $subtype)
];
if ($vars['params']['container_guid']) {
	$url_options["href"] .= "&container_guid=" . $vars['params']['container_guid'];
}

echo elgg_view("output/url", $url_options);

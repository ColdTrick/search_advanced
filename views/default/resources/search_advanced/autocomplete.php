<?php

$q = get_input('q');

header('Content-Type: application/json');

if (empty($q)) {
	echo json_encode([]);
	
	exit();
}

$params = [
	'query' => $q,
	'limit' => max((int) get_input('limit', 5), 1),
	'page_owner_guid' => (int) get_input('page_owner_guid'),
	'entity_guid' => (int) get_input('entity_guid'),
	'route_name' => get_input('route_name'),
];
$result = elgg_trigger_plugin_hook('autocomplete', 'search_advanced', $params, []);

echo json_encode(array_values($result));

exit();

<?php

$q = get_input('q');

header('Content-Type: application/json');

$result = [];
if (empty($q)) {
	echo json_encode([]);
	
	exit();
}
	
// let other plugins add content
$params = [
	'query' => $q,
	'limit' => max((int) get_input('limit', 5), 1),
	'page_owner_guid' => (int) get_input('page_owner_guid'),
];
$result = elgg_trigger_plugin_hook('autocomplete', 'search_advanced', $params, $result);

if (!empty($result)) {
	$result[] = [
		'type' => 'placeholder',
		'content' => '<label>' . elgg_echo('search_advanced:autocomplete:all') . '</label>',
		'href' => elgg_normalize_url('search?q=' . $q),
	];
}

echo json_encode(array_values($result));

exit();

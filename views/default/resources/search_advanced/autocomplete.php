<?php

$q = sanitise_string(get_input("q"));
$limit = max((int) get_input("limit", 5), 1);
$page_owner_guid = (int) get_input("page_owner_guid");

header("Content-Type: application/json");

$result = [];
if (empty($q)) {
	echo json_encode([]);
	
	exit();
}

$base_options = [
	'query' => $q,
	'limit' => $limit,
];

// look for users
$user_options = $base_options;
$user_options['type'] = 'user';

$users = elgg_search($user_options);
$users_count = 0;

if (count($users)) {
	$user_options['count'] = true;
	$users_count = elgg_search($user_options);
	
	$result[] = [
		'type' => 'placeholder',
		'content' => '<label>' . elgg_echo('item:user') . " ({$users_count})</label>",
		'href' => elgg_normalize_url('search?entity_type=user&search_type=entities&q=' . $q),
	];
	foreach ($users as $user) {
		$result[] = [
			'type' => 'user',
			'value' => $user->getDisplayName(),
			'href' => $user->getURL(),
			'content' => elgg_view('input/autocomplete/item', [
				'entity' => $user,
				'input_name' => 'search_user',
			])
		];
	}
}

// search for groups
$group_options = $base_options;
$group_options['type'] = 'group';

$groups = elgg_search($group_options);
$groups_count = 0;

if (count($groups)) {
	$group_options['count'] = true;
	$groups_count = elgg_search($group_options);
	
	$result[] = [
		'type' => 'placeholder',
		'content' => '<label>' . elgg_echo('item:group') . ' (' . $groups_count . ')</label>',
		'href' => elgg_normalize_url('search?entity_type=group&search_type=entities&q=' . $q),
	];
	foreach ($groups as $group) {
		$result[] = [
			'type' => 'group',
			'value' => $group->getDisplayName(),
			'href' => $group->getURL(),
			'content' => elgg_view('input/autocomplete/item', [
				'entity' => $group,
				'input_name' => 'search_group',
			]),
		];
	}
}
	
// let other plugins add content
$params = [
	'query' => $q,
	'limit' => $limit,
	'page_owner_guid' => $page_owner_guid,
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

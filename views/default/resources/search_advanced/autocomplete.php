<?php

$q = sanitise_string(get_input("q"));
$limit = max((int) get_input("limit", 5), 1);
$page_owner_guid = (int) get_input("page_owner_guid");

$result = array();
if (!empty($q)) {
	// look for users
	$options = array();
	$options['query'] = $q;
	$options['type'] = "user";
	$options['limit'] = $limit;
	
	$results = elgg_trigger_plugin_hook('search', 'user', $options, array());
	$user_count = $results['count'];
	$users = $results['entities'];
	
	if ($user_count > 0) {
		$result[] = array(
			"type" => "placeholder",
			"content" => "<label>" . elgg_echo("item:user") . " (" . $user_count . ")</label>",
			"href" => elgg_normalize_url("search?entity_type=user&search_type=entities&q=" . $q)
		);
		foreach ($users as $user) {
			$result[] = array(
				"type" => "user",
				"value" => $user->name,
				"href" => $user->getURL(),
				"content" => elgg_view("search_advanced/autocomplete/user", array("entity" => $user))
			);
		}
	}
	
	// search for groups
	$options = array();
	$options['query'] = $q;
	$options['type'] = "group";
	$options['limit'] = $limit;
	
	$results = elgg_trigger_plugin_hook('search', 'group', $options, array());
	$group_count = $results['count'];
	$groups = $results['entities'];
	
	if ($group_count > 0) {
		$result[] = array(
			"type" => "placeholder",
			"content" => "<label>" . elgg_echo("item:group") . " (" . $group_count . ")</label>",
			"href" => elgg_normalize_url("search?entity_type=group&search_type=entities&q=" . $q)
		);
		foreach ($groups as $group) {
			$result[] = array(
				"type" => "group",
				"value" => $group->name,
				"href" => $group->getURL(),
				"content" => elgg_view("search_advanced/autocomplete/group", array("entity" => $group)
			));
		}
	}
	
	// let other plugins add content
	$params = array(
		"query" => $q,
		"limit" => $limit,
		"page_owner_guid" => $page_owner_guid
	);
	$result = elgg_trigger_plugin_hook("autocomplete", "search_advanced", $params, $result);
	
	if (!empty($result)) {
		$result[] = array(
			"type" => "placeholder",
			"content" => "<label>" . elgg_echo("search_advanced:autocomplete:all") . "</label>",
			"href" => elgg_normalize_url("search?q=" . $q)
		);
	}
}

header("Content-Type: application/json");
echo json_encode(array_values($result));

exit();

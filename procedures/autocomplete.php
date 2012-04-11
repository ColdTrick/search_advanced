<?php

$q = get_input("q");
$limit = (int) get_input("limit", 50);
$page_owner_guid = (int) get_input("page_owner_guid");

$result = array();

$options = array(
	"type" => "user",
	"limit" => 5
);

if($users = elgg_get_entities($options)){
	$result[] = array("type" => "placeholder", "content" => "<label>Users</label>");
	foreach($users as $user){
		$result[] = array("type" => "user", "value" => $user->name, "href" => $user->getURL(), "content" => elgg_view("search_advanced/autocomplete/user", array("entity" => $user)));
	}
}

$options["type"] = "group";
if($groups = elgg_get_entities($options)){
	$result[] = array("type" => "placeholder", "content" => "<label>Groups</label>");
	foreach($groups as $group){
		$result[] = array("type" => "group", "value" => $group->name, "href" => $group->getURL(), "content" => elgg_view("search_advanced/autocomplete/group", array("entity" => $group)));
	}
}

header("Content-Type: application/json");
echo json_encode(array_values($result));
exit();
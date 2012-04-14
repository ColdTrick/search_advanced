<?php

$q = sanitise_string(get_input("q"));
$limit = (int) get_input("limit", 50);
$page_owner_guid = (int) get_input("page_owner_guid");

$result = array();
if(!empty($q)){
	
	$logged_in_user = elgg_get_logged_in_user_entity();
	
	// look for users
	$options = array(
		"type" => "user",
		"limit" => 5,
		"joins" => array("JOIN " . elgg_get_config("dbprefix") . "users_entity ue ON e.guid = ue.guid"),
		"wheres" => array("(ue.name like '%" . $q . "%' OR ue.username like '%" . $q . "%')")
	);
	
	if($logged_in_user){
		// look only in friends
		$options["relationship"] = "friend_of";
		$options["relationship_guid"] = $logged_in_user->getGUID();
		$users = elgg_get_entities_from_relationship($options);
	}
	
	if(!$users){
		// no friends or logged out
		$options["relationship"] = "member_of_site";
		$options["relationship_guid"] = elgg_get_site_entity()->getGUID();
		$users = elgg_get_entities($options);
	}
	
	if($users){
		$result[] = array("type" => "placeholder", "content" => "<label>" . elgg_echo("item:user") . "</label>");
		foreach($users as $user){
			$result[] = array("type" => "user", "value" => $user->name, "href" => $user->getURL(), "content" => elgg_view("search_advanced/autocomplete/user", array("entity" => $user)));
		}
	}
	
	// search for groups
	$options = array(
		"type" => "group",
		"limit" => 5,
		"joins" => array("JOIN " . elgg_get_config("dbprefix") . "groups_entity ge ON e.guid = ge.guid"),
		"wheres" => array("ge.name like '%" . $q . "%'")
	);
	
	if($logged_in_user){
		// look only in personal groups
		$options["relationship"] = "member";
		$options["relationship_guid"] = $logged_in_user->getGUID();
		$groups = elgg_get_entities_from_relationship($options);
	}
	
	if(!$groups){
		// no groups or logged out
		unset($options["relationship"]);
		unset($options["relationship_guid"]);
		$groups = elgg_get_entities($options);
	}
	
	if($groups){
		$result[] = array("type" => "placeholder", "content" => "<label>" . elgg_echo("item:group") . "</label>");
		foreach($groups as $group){
			$result[] = array("type" => "group", "value" => $group->name, "href" => $group->getURL(), "content" => elgg_view("search_advanced/autocomplete/group", array("entity" => $group)));
		}
	}
}	

header("Content-Type: application/json");
echo json_encode(array_values($result));

exit();
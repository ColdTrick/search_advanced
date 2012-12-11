<?php

$q = sanitise_string(get_input("q"));
$limit = max((int) get_input("limit", 5), 1);
$page_owner_guid = (int) get_input("page_owner_guid");

$result = array();
if(!empty($q)){
	
	// keywords (tags, categories)
	$keywords = search_advanced_get_keywords();
	if(!empty($keywords)){
		foreach($keywords as $content){
			if(stristr($content, $q)){
				$result[] = array("type" => "tag", "content" => search_highlight_words(array($q), $content), "value" => $content, "href" => elgg_normalize_url("search?q=" . urlencode($content)));
			}
		}
	}
	
	$logged_in_user = elgg_get_logged_in_user_entity();
	
	// look for users
	$options = array(
		"type" => "user",
		"limit" => $limit,
		"joins" => array("JOIN " . elgg_get_config("dbprefix") . "users_entity ue ON e.guid = ue.guid"),
		"wheres" => array("(ue.name like '%" . $q . "%' OR ue.username like '%" . $q . "%')")
	);
	
	if($logged_in_user){
		// look only in friends
		$options["relationship"] = "friend";
		$options["relationship_guid"] = $logged_in_user->getGUID();
		$users = elgg_get_entities_from_relationship($options);
	}
	
	if(!$users || (count($users) < $limit)){
		// no friends or logged out or fill up to limit
		if($users){
			// exclude already found users
			$options["limit"] = $limit - count($users);
			$guids = array();
			foreach($users as $tmp_user){
				$guids[] = $tmp_user->getGUID();
			}
			$options["wheres"][] = "e.guid NOT IN (" . implode(",", $guids) . ")";
		}
		$options["relationship"] = "member_of_site";
		$options["relationship_guid"] = elgg_get_site_entity()->getGUID();
		$options["inverse_relationship"] = true;
		
		if($site_users = elgg_get_entities_from_relationship($options)){
			if($users){
				$users = array_merge($users, $site_users);
			} else {
				$users = $site_users;
			}
		}
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
		"limit" => $limit,
		"joins" => array("JOIN " . elgg_get_config("dbprefix") . "groups_entity ge ON e.guid = ge.guid"),
		"wheres" => array("ge.name like '%" . $q . "%'")
	);
	
	if($logged_in_user){
		// look only in personal groups
		$options["relationship"] = "member";
		$options["relationship_guid"] = $logged_in_user->getGUID();
		$groups = elgg_get_entities_from_relationship($options);
	}
	
	if(!$groups || (count($groups) < $limit)){
		// no groups or logged out
		if($groups){
			// exclude already found groups
			$options["limit"] = $limit - count($groups);
			$guids = array();
			foreach($groups as $tmp_group){
				$guids[] = $tmp_group->getGUID();
			}
			$options["wheres"][] = "e.guid NOT IN (" . implode(",", $guids) . ")";
		}
		
		unset($options["relationship"]);
		unset($options["relationship_guid"]);
		
		if($site_groups = elgg_get_entities($options)){
			if($groups){
				$groups = array_merge($groups, $site_groups);
			} else {
				$groups = $site_groups;
			}
		}
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
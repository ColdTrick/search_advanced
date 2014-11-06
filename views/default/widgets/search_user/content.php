<?php
$form_body = elgg_view("search_advanced/search/user", $vars);
if ($form_body) {
	
	$form_body .= elgg_view("input/hidden", array("name" => "search_type", "value" => "entities"));
	$form_body .= elgg_view("input/hidden", array("name" => "entity_type", "value" => "user"));
	
	echo elgg_view("input/form", array(
		"action" => "search",
		"method" => "GET",
		"disable_security" => true,
		"body" => $form_body
	));
} else {
	echo elgg_echo("search_advanced:widgets:search_user:no_results");
}
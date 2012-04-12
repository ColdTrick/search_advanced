<?php
	
	global $CONFIG;

	$widget = $vars["entity"];
		
	$form_body .= elgg_view("input/text", array("name" => "q"));
	
	$form_body .= elgg_view("input/submit", array("value" => elgg_echo("search"), "class" => "hidden"));
	
	$form = elgg_view("input/form", array("body" => $form_body, "action" => "/search_advanced/widget_search", "disable_security" => true, "class" => "search-advanced-widget-search-form"));

	echo $form;
	
	echo "<div class='search-advanced-widget-search-results'></div>";

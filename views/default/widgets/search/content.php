<?php

	$widget = $vars["entity"];

	$form_body = elgg_view("input/text", array("name" => "q"));

	$type = "";
	$types = $widget->types;
	if (!empty($types)) {
		list($type, $subtype) = explode(":" , $types);
	}

	if (!empty($type)) {
		$form_body .= elgg_view("input/hidden", array("name" => "search_type", "value" => "entities"));
		$form_body .= elgg_view("input/hidden", array("name" => "entity_type", "value" => $type));
		if (!empty($subtype)) {
			$form_body .= elgg_view("input/hidden", array("name" => "entity_subtype", "value" => $subtype));
		}
	}

	$form_body .= elgg_view("input/submit", array("value" => elgg_echo("search"), "class" => "hidden"));

	$form = elgg_view("input/form", array("body" => $form_body, "action" => "/search", "disable_security" => true, "class" => "search-advanced-widget-search-form"));

	echo $form;

	echo "<div class='search-advanced-widget-search-results'></div>";

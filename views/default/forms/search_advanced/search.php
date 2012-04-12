<?php

if($vars["search_type"]){
	echo elgg_view("input/hidden", array("name" => "search_type", "value" => $vars["search_type"]));
}

if($vars["type"]){
	echo elgg_view("input/hidden", array("name" => "entity_type", "value" => $vars["type"]));
}

if($vars["subtype"]){
	echo elgg_view("input/hidden", array("name" => "entity_subtype", "value" => $vars["subtype"]));
}

echo elgg_view("input/text", array("name" => "q", "value" => $vars["query"]));
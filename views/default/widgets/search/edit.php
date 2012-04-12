<?php

	/*$widget = $vars["entity"];

	$types = get_registered_entity_types();
	$options_values = array();
	
	$options_values[0] = elgg_echo("all");
	foreach ($types as $type => $subtypes) {
		// @todo when using index table, can include result counts on each of these.
		if (is_array($subtypes) && count($subtypes)) {
			foreach ($subtypes as $subtype) {
				if($subtype == "page_top"){
					continue;
				}
	
				if($type == "custom"){
					$option = elgg_echo("search_types:$subtype");
				} else {
					$option = elgg_echo("item:$type:$subtype");
				}
				$value = "$type:$subtype";
				$options_values[$value] = $option;
			}
				
		} else {
			$option = elgg_echo("item:$type");
			$value = "$type";
			$options_values[$value] = $option;
		}
	}
	
	echo elgg_view("input/dropdown", array("name" => "params[types]", "value" => $widget->types, "options_values" => $options_values));
	echo "<br />";
	echo elgg_echo("filter");
	echo elgg_view("input/tags", array("name" => "params[tag_filter]", "value" => $widget->tag_filter));
	*/
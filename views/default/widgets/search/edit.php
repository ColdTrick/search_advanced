<?php

$widget = $vars["entity"];

$types = get_registered_entity_types();
$options_values = array();

$options_values[0] = elgg_echo("all");
foreach ($types as $type => $subtypes) {
	if ($widget->getOwnerEntity() instanceof ElggGroup && $type == "group") {
		continue;
	}
	
	// @todo when using index table, can include result counts on each of these.
	if (is_array($subtypes) && count($subtypes)) {
		foreach ($subtypes as $subtype) {
			if ($subtype == "page_top") {
				continue;
			}

			if ($type == "custom") {
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

$submit_behaviour_options = array(
	"show_in_widget" => elgg_echo("search_advanced:widgets:search:edit:submit_behaviour:show_in_widget"),
	"go_to_search" => elgg_echo("search_advanced:widgets:search:edit:submit_behaviour:go_to_search"),
);

echo "<div>";
echo elgg_echo("filter") . "&nbsp;";
echo elgg_view("input/dropdown", array("name" => "params[types]", "value" => $widget->types, "options_values" => $options_values));
echo "</div>";

echo "<div>";
echo elgg_echo("search_advanced:widgets:search:edit:submit_behaviour") . "&nbsp;";
echo elgg_view("input/dropdown", array("name" => "params[submit_behaviour]", "value" => $widget->submit_behaviour, "options_values" => $submit_behaviour_options));
echo "</div>";

<?php

$widget = elgg_extract('entity', $vars);

$types = get_registered_entity_types();
$options_values = [0 => elgg_echo('all')];
foreach ($types as $type => $subtypes) {
	if ($widget->getOwnerEntity() instanceof ElggGroup && $type == 'group') {
		continue;
	}
	
	if (is_array($subtypes) && count($subtypes)) {
		foreach ($subtypes as $subtype) {
			if ($type == 'custom') {
				$option = elgg_echo("search_types:$subtype");
			} else {
				$option = elgg_echo("item:$type:$subtype");
			}
			
			$options_values["{$type}:{$subtype}"] = $option;
		}
	} else {
		$options_values[$type] = elgg_echo("item:{$type}");
	}
}

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('filter'),
	'name' => 'params[types]',
	'value' => $widget->types,
	'options_values' => $options_values,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('search_advanced:widgets:search:edit:submit_behaviour'),
	'name' => 'params[submit_behaviour]',
	'value' => $widget->submit_behaviour,
	'options_values' => [
		'show_in_widget' => elgg_echo('search_advanced:widgets:search:edit:submit_behaviour:show_in_widget'),
		'go_to_search' => elgg_echo('search_advanced:widgets:search:edit:submit_behaviour:go_to_search'),
	],
]);

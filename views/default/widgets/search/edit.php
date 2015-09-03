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
			$value = "$type:$subtype";
			$options_values[$value] = $option;
		}
			
	} else {
		$option = elgg_echo("item:$type");
		$value = "$type";
		$options_values[$value] = $option;
	}
}

$submit_behaviour_options = [
	'show_in_widget' => elgg_echo('search_advanced:widgets:search:edit:submit_behaviour:show_in_widget'),
	'go_to_search' => elgg_echo('search_advanced:widgets:search:edit:submit_behaviour:go_to_search'),
];


$filter_body = elgg_echo('filter') . '&nbsp;';
$filter_body .= elgg_view('input/dropdown', [
	'name' => 'params[types]',
	'value' => $widget->types,
	'options_values' => $options_values
]);

echo elgg_format_element('div', [], $filter_body);

$submit_behaviour_body = elgg_echo('search_advanced:widgets:search:edit:submit_behaviour') . '&nbsp;';
$submit_behaviour_body .= elgg_view('input/dropdown', [
	'name' => 'params[submit_behaviour]',
	'value' => $widget->submit_behaviour,
	'options_values' => $submit_behaviour_options
]);

echo elgg_format_element('div', [], $submit_behaviour_body);

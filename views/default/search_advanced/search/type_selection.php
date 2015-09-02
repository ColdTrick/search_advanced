<?php

if (elgg_is_xhr()) {
	echo elgg_format_element('script', [], 'require(["search_advanced/type_selection"]);');
} else {
	elgg_require_js('search_advanced/type_selection');
}

$types = get_registered_entity_types();
$custom_types = elgg_trigger_plugin_hook('search_types', 'get_types', array(), array());

$current_selected = elgg_echo('all');

$entity_type = get_input('entity_type');
$entity_subtype = get_input('entity_subtype');
$search_type = get_input('search_type');

if (!in_array($search_type, $custom_types)) {
	$search_type = '';
}

if (!array_key_exists($entity_type, $types)) {
	$entity_type = '';
}

if (array_key_exists($entity_type, $types) && !in_array($entity_subtype, $types[$entity_type])) {
	$entity_subtype = '';
}

$results = '';
if (!empty($search_type) && ($search_type !== 'entities')) {
	$current_selected = elgg_echo("search_types:{$search_type}");
	
	$results .= elgg_view('input/hidden', ['name' => 'search_type', 'value' => $search_type]);
	$results .= elgg_view('input/hidden', ['name' => 'entity_type', 'disabled' => true]);
	$results .= elgg_view('input/hidden', ['name' => 'entity_subtype', 'disabled' => true]);
} elseif (!empty($entity_type)) {
	$results .= elgg_view('input/hidden', ['name' => 'search_type', 'value' => 'entities']);
	$results .= elgg_view('input/hidden', ['name' => 'entity_type', 'value' => $entity_type]);
	
	$current_selected = elgg_echo("item:{$entity_type}");
	
	if (!empty($entity_subtype)) {
		$current_selected = elgg_echo("item:{$entity_type}:{$entity_subtype}");
		
		$results .= elgg_view('input/hidden', ['name' => 'entity_subtype', 'value' => $entity_subtype]);
	} else {
		$results .= elgg_view('input/hidden', ['name' => 'entity_subtype', 'disabled' => true]);
	}
} else {
	$results .= elgg_view('input/hidden', ['name' => 'search_type', 'value' => 'entities', 'disabled' => true]);
	$results .= elgg_view('input/hidden', ['name' => 'entity_type', 'disabled' => true]);
	$results .= elgg_view('input/hidden', ['name' => 'entity_subtype', 'disabled' => true]);
}

$results .= '<ul class="search-advanced-type-selection"><li>';
$results .= '<a>' .  $current_selected . '</a>';
$results .= elgg_view_menu('search_type_selection', ['class' => 'search-advanced-type-selection-dropdown']);
$results .= '</li></ul>';

echo $results;

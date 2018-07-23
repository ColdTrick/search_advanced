<?php

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

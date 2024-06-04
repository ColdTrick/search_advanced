<?php
$vars['show_button'] = true;
$form_body = elgg_view('search/filter', $vars);
if (empty($form_body)) {
	return;
}

$form_body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'search_type',
	'value' => get_input('search_type'),
]);
$form_body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'entity_type',
	'value' => get_input('entity_type'),
]);
$form_body .= elgg_view_field([
	'#type' => 'hidden',
	'name' => 'entity_subtype',
	'value' => get_input('entity_subtype'),
]);

echo elgg_view('input/form', [
	'method' => 'GET',
	'disable_security' => true,
	'action' => 'search',
	'body' => $form_body,
	'class' => 'search-advanced-user-search'
]);

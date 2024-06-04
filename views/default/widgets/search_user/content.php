<?php
$params = $vars;
$params['show_placeholder'] = true;
$params['show_label'] = false;
$params['show_button'] = true;
$params['module_type'] = false;

$form_body = elgg_view('search/filter/entities/user/user', $params);
if ($form_body) {
	$form_body .= elgg_view_field([
		'#type' => 'hidden',
		'name' => 'search_type',
		'value' => 'entities',
	]);
	$form_body .= elgg_view_field([
		'#type' => 'hidden',
		'name' => 'entity_type',
		'value' => 'user',
	]);
	$form_body .= elgg_view_field([
		'#type' => 'hidden',
		'name' => 'entity_subtype',
		'value' => 'user',
	]);
	
	echo elgg_view('input/form', [
		'action' => 'search',
		'method' => 'GET',
		'disable_security' => true,
		'body' => $form_body,
		'class' => 'search-advanced-user-search'
	]);
} else {
	echo elgg_echo('widgets:search_user:no_results');
}

<?php
$params = $vars;
$params['show_placeholder'] = true;
$params['show_label'] = false;
$params['show_button'] = true;

$form_body = elgg_view('search/filter/entities/user', $params);
if ($form_body) {
	
	$form_body .= elgg_view('input/hidden', ['name' => 'search_type', 'value' => 'entities']);
	$form_body .= elgg_view('input/hidden', ['name' => 'entity_type', 'value' => 'user']);
	
	echo elgg_view('input/form', [
		'action' => 'search',
		'method' => 'GET',
		'disable_security' => true,
		'body' => $form_body,
		'class' => 'search-advanced-user-search'
	]);
} else {
	echo elgg_echo('search_advanced:widgets:search_user:no_results');
}
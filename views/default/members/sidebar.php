<?php
/**
 * Members sidebar
 */

$form_body = elgg_view_field([
	'#type' => 'search',
	'name' => 'q',
	'placeholder' => elgg_echo('search_advanced:members:sidebar:q:placeholder'),
	'aria-label' => elgg_echo('members:search'), // because we don't add #label
]);

$form_body .= elgg_view('search/filter/entities/user/user', [
	'show_button' => false,
	'show_label' => false,
	'show_placeholder' => true,
	'module_type' => false,
]);

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
$form_body .= elgg_view_field([
	'#type' => 'submit',
	'text' => elgg_echo('search'),
	'icon' => 'search',
]);

$body = elgg_view('input/form', [
	'method' => 'GET',
	'disable_security' => true,
	'action' => 'search',
	'body' => $form_body,
	'class' => 'search-advanced-user-search',
	'role' => 'search',
	'aria-label' => elgg_echo('members:aria:label:member_search'),
]);

echo elgg_view_module('aside', elgg_echo('members:search'), $body);

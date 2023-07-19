<?php
/**
 * Members sidebar
 */

$form_body = elgg_format_element('div', [], elgg_view('input/text', ['name' => 'q', 'placeholder' => elgg_echo('search_advanced:members:sidebar:q:placeholder')]));

$form_body .= elgg_view('search/filter/entities/user/user', [
	'show_button' => false,
	'show_label' => false,
	'show_placeholder' => true,
	'module_type' => false,
]);

$form_body .= '<div>';
$form_body .= elgg_view('input/hidden', ['name' => 'search_type', 'value' => 'entities']);
$form_body .= elgg_view('input/hidden', ['name' => 'entity_type', 'value' => 'user']);
$form_body .= elgg_view('input/submit', ['text' => elgg_echo('search')]);
$form_body .= '</div>';

$body = elgg_view('input/form', [
	'method' => 'GET',
	'disable_security' => true,
	'action' => 'search',
	'body' => $form_body,
	'class' => 'search-advanced-user-search'
]);

echo elgg_view_module('aside', elgg_echo('members:search'), $body);

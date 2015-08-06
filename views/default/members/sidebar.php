<?php
/**
 * Members sidebar
 */


$form_body = elgg_view('input/text', ['name' => 'q']);

$form_body .= elgg_view('search_advanced/search/user', [
	'show_button' => false,
	'show_label' => false,
	'show_placeholder' => true,
	'soundex_newline' => true,
]);

$form_body .= '<div class="mtm">';
$form_body .= elgg_view('input/hidden', ['name' => 'search_type', 'value' => 'entities']);
$form_body .= elgg_view('input/hidden', ['name' => 'entity_type', 'value' => 'user']);
$form_body .= elgg_view('input/submit', ['value' => elgg_echo('search')]);
$form_body .= '</div>';

$body = elgg_view('input/form', [
	'method' => 'GET',
	'disable_security' => true,
	'action' => 'search',
	'body' => $form_body,
]);

echo elgg_view_module('aside', elgg_echo('members:search'), $body);

<?php
/**
 * Search box
 *
 * @uses $vars["value"] Current search query
 * @uses $vars["class"] Additional class
 */

if (elgg_extract('search_autocomplete', $vars, true)) {
	elgg_require_js('search_advanced/autocomplete');
}

$placeholder = elgg_extract('placeholder', $vars, elgg_echo('search_advanced:searchbox'));
$container_entity = elgg_extract('container_entity', $vars);

$value = elgg_extract('value', $vars, get_input('q', get_input('tag', NULL)));

$vars['class'] = (array) elgg_extract('class', $vars, []);
$vars['class'][] = 'elgg-search';
$vars['class'][] = 'ui-front';

// @todo - why the strip slashes?
$value = stripslashes($value);

// @todo - create function for sanitization of strings for display in 1.8
// encode <,>,&, quotes and characters above 127
if (function_exists("mb_convert_encoding")) {
	$display_query = mb_convert_encoding($value, "HTML-ENTITIES", "UTF-8");
} else {
	// if no mbstring extension, we just strip characters
	$display_query = preg_replace("/[^\x01-\x7F]/", "", $value);
}
$display_query = htmlspecialchars($display_query, ENT_QUOTES, "UTF-8", false);

$form_body = '<table><tr>';
if (elgg_extract('show_type_selection', $vars, true)) {
	$form_body .= '<td>' . elgg_view("search_advanced/search/type_selection") . '</td>';
}
$form_body .= '<td>';
$form_body .= elgg_view('input/text', [
	'class' => 'search-input',
	'size' => '21',
	'name' => 'q',
	'value' => $display_query,
	'placeholder' => $placeholder,
	'required' => true
]);
$form_body .= elgg_view('input/button', [
	'type' => 'submit',
	'value' => elgg_echo("search:go"),
	'class' => 'search-submit-button'
]);
$form_body .= '</td>';
$form_body .= '</tr></table>';

if (elgg_instanceof($container_entity, 'group')) {
	$form_body .= elgg_view('input/hidden', array(
		'name' => 'container_guid',
		'value' => $container_entity->guid
	));
}
		
echo elgg_view('input/form', [
	'class' => $vars['class'],
	'action' => 'search',
	'method' => 'GET',
	'disable_security' => true,
	'body' => $form_body,
]);

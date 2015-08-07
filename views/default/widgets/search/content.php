<?php

$widget = $vars['entity'];
$container_entity = $widget->getContainerEntity();

$form_body = elgg_view('input/text', ['name' => 'q', 'required' => true]);

$type = '';
$types = $widget->types;
if (!empty($types)) {
	list($type, $subtype) = explode(':' , $types);
}

if (!empty($type)) {
	$form_body .= elgg_view('input/hidden', ['name' => 'search_type', 'value' => 'entities']);
	$form_body .= elgg_view('input/hidden', ['name' => 'entity_type', 'value' => $type]);
	if (!empty($subtype)) {
		$form_body .= elgg_view('input/hidden', ['name' => 'entity_subtype', 'value' => $subtype]);
	}
}

$form_body .= elgg_view('input/submit', ['value' => elgg_echo('search'), 'class' => 'hidden']);

$form_options = [
	'body' => $form_body,
	'action' => '/search',
	'disable_security' => true
];

if ($container_entity instanceof ElggGroup) {
	$form_options['action'] .= '?container_guid=' . $container_entity->getGUID();
}

if ($widget->submit_behaviour !== 'go_to_search') {
	if (elgg_is_xhr()) {
		echo elgg_format_element('script', [], 'require(["search_advanced/widgets/search"]);');	
	} else {
		elgg_require_js('search_advanced/widgets/search');
	}
	$form_options['class'] = 'search-advanced-widget-search-form';
}

echo elgg_view('input/form', $form_options);

echo elgg_format_element('div', ['class' => 'search-advanced-widget-search-results']);

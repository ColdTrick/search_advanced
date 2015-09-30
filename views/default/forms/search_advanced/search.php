<?php

$search_type = elgg_extract('search_type', $vars);
$type = elgg_extract('type', $vars);
$subtype = elgg_extract('subtype', $vars);
$container_guid = (int) elgg_extract('container_guid', $vars);
$query = elgg_extract('query', $vars);

if ($search_type) {
	echo elgg_view("input/hidden", ["name" => "search_type", "value" => $search_type]);
}

if ($type) {
	echo elgg_view("input/hidden", ["name" => "entity_type", "value" => $type]);
}

if ($subtype) {
	echo elgg_view("input/hidden", ["name" => "entity_subtype", "value" => $subtype]);
}

echo elgg_view("input/text", ["name" => "q", "value" => $query , "class" => "ui-front mbs"]);

if ($container_guid) {
	$container_entity = get_entity($container_guid);
	if ($container_entity instanceof ElggGroup) {
		$container_link = elgg_view('output/url', [
			'text' => $container_entity->name,
			'href' => $container_entity->getURL(),
		]);
		
		$undo_url = search_advanced_get_search_url(['container_guid' => null]);
		
		$undo_container_link = elgg_view('output/url', [
			'text' => elgg_view_icon('delete'),
			'href' => $undo_url,
			'title' => elgg_echo('search_advanced:results:container:undo')
		]);
		
		$text = elgg_echo('search_advanced:results:container:title', [$container_link]);
		$text .= $undo_container_link;
		
		echo elgg_format_element('div', [], $text);
		
	}
	echo elgg_view("input/hidden", ["name" => "container_guid", "value" => $container_guid]);
}


$vars['filter_position'] = 'content';
$filter = elgg_view('search/filter', $vars);

$submit_options = [
	'value' => elgg_echo('submit'),
	'class' => 'hidden',
];

if (!empty($filter)) {
	echo $filter;
	unset($submit_options['class']);
}

echo elgg_view("input/submit", $submit_options);

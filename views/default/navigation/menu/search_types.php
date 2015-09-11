<?php

$display_format = elgg_get_plugin_setting('search_types_menu_format', 'search_advanced', 'menu');
$menu = elgg_extract('menu', $vars);
$selected_item = elgg_extract('selected_item', $vars);

if (empty($menu)) {
	return;
}

if ($display_format == 'menu') {
	$body = elgg_view('navigation/menu/page', $vars);
} else {
	
	$options = '';
	foreach ($menu as $section => $menu_items) {
		$group_options = '';
		foreach ($menu_items as $menu_item) {
			$selected = false;
			if ($selected_item && ($selected_item->getName() == $menu_item->getName())) {
				$selected = true;
			}
			$group_options .= elgg_format_element('option', ['value' => $menu_item->getHref(), 'selected' => $selected], $menu_item->getText());
		}
		
		if ($section == 'default') {
			$options .= $group_options;
		} else {
			$label = elgg_echo("menu:search_types:header:$section");
			$options .= elgg_format_element('optgroup', ['label' => $label], $group_options);
		}
	}
	
	$body = elgg_format_element('select', ['class' => 'search-advanced-search-types'], $options);
}

if (empty($body)) {
	return;
}

echo elgg_view_module('aside', elgg_echo('search_advanced:filter:refine'), $body);

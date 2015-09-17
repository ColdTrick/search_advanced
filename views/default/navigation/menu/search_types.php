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
		foreach ($menu_items as $menu_item) {
			$selected = false;
			if ($selected_item && ($selected_item->getName() == $menu_item->getName())) {
				$selected = true;
			}
			
			$children = $menu_item->getChildren();
			$text = $menu_item->getText();
			$option_options = [
				'value' => $menu_item->getHref(),
				'selected' => $selected,
			];
			
			if ($children) {
				$option_options['class'] = 'search-advanced-search-types-parent';
			}
			
			$options .= elgg_format_element('option', $option_options, $text);
						
			if (!$children) {
				continue;
			}
			
			foreach ($children as $child) {
				$selected = false;
				if ($selected_item && ($selected_item->getName() == $child->getName())) {
					$selected = true;
				}
				$options .= elgg_format_element('option', [
					'value' => $child->getHref(),
					'selected' => $selected,
					'class' => 'search-advanced-search-types-child',
				], $child->getText());
			}
		}
	}
	
	$body = elgg_format_element('select', ['class' => 'search-advanced-search-types'], $options);
}

if (empty($body)) {
	return;
}

echo elgg_view_module('aside', elgg_echo('search_advanced:filter:refine'), $body);

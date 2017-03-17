<?php
$vars['filter_position'] = 'sidebar';

$menu_vars = $vars;
$menu_vars['sort_by'] = elgg_extract('sort_by', $vars, 'name');

echo elgg_view_menu('search_types', $menu_vars);

$filter = elgg_view('search/filter', $vars);
if ($filter) {
	echo $filter;
	echo elgg_view('input/button', [
		'class' => 'search-advanced-search-sidebar-button elgg-button-submit',
		'value' => elgg_echo('search'),
	]);
}

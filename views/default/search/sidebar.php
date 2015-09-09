<?php
$vars['filter_position'] = 'sidebar';

$menu_vars = $vars;
$menu_vars['sort_by'] = 'name';

echo elgg_view_menu('search_types', $menu_vars);

echo elgg_view('search/filter', $vars);

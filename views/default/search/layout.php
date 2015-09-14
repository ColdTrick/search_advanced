<?php
/**
 * The default search layout
 *
 * @uses $vars['body']
 */

// show loader or direct page
$loader = (int) get_input('loader', 0);

$search_with_loader = false;
if (elgg_get_plugin_setting('search_with_loader', 'search_advanced') == 'yes') {
	$search_with_loader = true;
}

$title = elgg_extract('title', $vars);
$content = elgg_extract('body', $vars);
$class = elgg_extract('class', $vars);
$search_params = (array) elgg_extract('params', $vars, []);

// register menu items
search_advanced_register_menu_items($vars);

if (is_array($content)) {
	$content = implode('', $content);
}

if (empty($content)) {
	$content = elgg_view('search/no_results');
} else {
	$menu = elgg_view_menu('search_list', [
		'sort_by' => 'priority',
		'class' => 'float-alt elgg-menu-hz',
		'search_params' => $search_params,
	]);
	if (!empty($menu)) {
		$menu = '<div class="clearfix">' . $menu . '</div>';
		$content = $menu . $content;
	}
}

// add search form
if (!elgg_is_xhr() || ($search_with_loader && $loader)) {
	$form = elgg_view_form('search_advanced/search', [
		'action' => 'search',
		'method' => 'GET',
		'disable_security' => true
	], $search_params);
	
	$content = $form . $content;
}

if (!elgg_is_xhr() || $loader) {
	$sidebar = elgg_view('search/sidebar', $vars);
	
	echo elgg_view_layout('one_sidebar', [
		'title' => $title,
		'content' => $content,
		'sidebar' => $sidebar,
		'class' => $class
	]);
} else {
	echo $content;
}
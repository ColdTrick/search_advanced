<?php

$query = stripslashes(get_input('q', get_input('tag', '')));
$profile_filter = get_input('search_advanced_profile_fields');
$entity_type = get_input('entity_type');

// check that we have an actual query
if (!$query && !((count($profile_filter) > 0) && $entity_type == "user")) {
	$loader = (int) get_input('loader', 0);
	
	$search_with_loader = false;
	if (elgg_get_plugin_setting('search_with_loader', 'search_advanced') == 'yes') {
		$search_with_loader = true;
	}
	
	$title = elgg_echo('search:search_error');
	
	$body = '';
	if (!elgg_is_xhr() || ($search_with_loader && $loader)) {
		$body .= elgg_view_form('search_advanced/search', [
			'action' => 'search',
			'method' => 'GET',
			'disable_security' => true
		]);
	}
	
	$body .= elgg_echo('search:no_query');
	
	if (!elgg_is_xhr()) {
		// regular page
		$layout = elgg_view_layout('one_sidebar', ['title' => $title, 'content' => $body]);
		echo elgg_view_page(elgg_extract('page_title', $vars), $layout);
	} elseif (elgg_is_xhr() && $loader) {
		// ajax loaded search result page
		echo elgg_view_layout('one_sidebar', ['title' => $title, 'content' => $body]);
	} else {
		// ajax loaded but no search result page (probably widget)
		echo elgg_view_title($title) . $body;
	}
}

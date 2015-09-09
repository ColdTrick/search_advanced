<?php
/**
 * Elgg search page
 *
 * @todo much of this code should be pulled out into a library of functions
 */

// Search supports RSS
global $autofeed;
$autofeed = true;

// $search_type == all || entities || trigger plugin hook
$search_type = get_input('search_type', 'all');
$entity_type = get_input('entity_type');

// @todo there is a bug in get_input that makes variables have slashes sometimes.
// @todo is there an example query to demonstrate ^
// XSS protection is more important that searching for HTML.
$query = stripslashes(get_input('q', get_input('tag', '')));
$profile_filter = get_input('search_advanced_profile_fields');
$profile_soundex = get_input('search_advanced_profile_fields_soundex');

// @todo - create function for sanitization of strings for display in 1.8
// encode <,>,&, quotes and characters above 127
if (function_exists('mb_convert_encoding')) {
	$display_query = mb_convert_encoding($query, 'HTML-ENTITIES', 'UTF-8');
} else {
	// if no mbstring extension, we just strip characters
	$display_query = preg_replace("/[^\x01-\x7F]/", "", $query);
}

$display_query = htmlspecialchars($display_query, ENT_QUOTES, 'UTF-8', false);

if (empty($display_query)) {
	$page_title = elgg_echo('search:results', ['']);
} else {
	$page_title = elgg_echo('search:results', ["\"$display_query\""]);
}

$vars['page_title'] = $page_title;

// show loader or direct page
$loader = (int) get_input('loader', 0);

$search_with_loader = false;
if (elgg_get_plugin_setting('search_with_loader', 'search_advanced') == 'yes') {
	$search_with_loader = true;
}

if ($search_with_loader && !elgg_is_xhr()) {
	// show loader
	$page_data = elgg_view_layout('one_column', ['content' => elgg_view('search_advanced/loader')]);
	
	echo elgg_view_page($page_title, $page_data);
	return;
}

// check and show error page
$error_output = elgg_view('search_advanced/error', $vars);
if ($error_output) {
	echo $error_output;
	return;
}

if (search_advanced_get_list_type() == 'compact') {
	$limit = ($search_type == 'all') ? 5 : get_input('limit', 20);
} else {
	$limit = ($search_type == 'all') ? 2 : get_input('limit', 10);
}


// set up search params
$params = array(
	'query' => $query,
	'offset' => ($search_type == 'all') ? 0 : get_input('offset', 0),
	'limit' => $limit,
	'sort' => get_input('sort', 'relevance'),
	'order' => get_input('order', 'desc'),
	'search_type' => $search_type,
	'type' => $entity_type,
	'subtype' => get_input('entity_subtype'),
	'owner_guid' => get_input('owner_guid'),
	'container_guid' => get_input('container_guid'),
	'pagination' => ($search_type == 'all') ? false : true,
	'profile_filter' => $profile_filter,
	'profile_soundex' => $profile_soundex
);

$params = elgg_trigger_plugin_hook('search_params', 'search', $params, $params);

$types = get_registered_entity_types();
$custom_types = elgg_trigger_plugin_hook('search_types', 'get_types', $params, array());

$object_types = elgg_extract('object', $types);
if ($object_types) {
	// the sidebar menu shows objects below other entity types
	// by moving the object types to the end of the array this will also
	// make sure that on the search index page they are also listed last
	unset($types['object']);
	$types['object'] = $object_types;
}

// to pass the correct current search type to the views
$current_params = $params;
$current_params['search_type'] = 'entities';
// foreach through types.
// if a plugin returns FALSE for subtype ignore it.
// if a plugin returns NULL or '' for subtype, pass to generic type search function.
// if still NULL or '' or empty(array()) no results found. (== don't show??)

$combine_search_results = false;
if (elgg_get_plugin_setting('combine_search_results', 'search_advanced') == 'yes') {
	$combine_search_results = true;
}

// start the actual search
$search_result_counters = [];
$results_html = [];

foreach ($types as $type => $subtypes) {
	if ($type !== "object") {
		if ($type !== "user" && empty($params["query"])) {
			continue;
		}
		
		if ($type == "user" && empty($params["query"]) && empty($profile_filter)) {
			continue;
		}
		// pull in default type entities with no subtypes
		$current_params['type'] = $type;
		$current_params['subtype'] = ELGG_ENTITIES_ANY_VALUE;

		unset($current_params['search_advanced_count_only']);
		if ($search_type != 'all' && $entity_type != $type) {
			// only want count if doing specific search
			$current_params['search_advanced_count_only'] = true;
		}
		
		$results = elgg_trigger_plugin_hook('search', $type, $current_params, array());
		if (!$results) {
			// if $results = FALSE => someone is saying not to display these types in searches.
			continue;
		}

		// save result count
		$search_result_counters["item:" . $type] = $results['count'];
		
		if (!is_array($results['entities']) || empty($results['count'])) {
			// no results, so no need for any output
			continue;
		}
			
		$view = search_get_search_view($current_params, 'list');
		if (!$view) {
			// no output view, so skip this
			continue;
		}
					
		if ($current_params['search_advanced_count_only'] === true) {
			// only interested in count so skip to next
			continue;
		}
			
		$results_html["item:" . $type] = elgg_view($view, [
			'results' => $results,
			'params' => $current_params,
		]);
	}
	
	// $type = object
	if (empty($subtypes) || !is_array($subtypes)) {
		continue;
	}
	
	if (empty($params['query'])) {
		continue;
	}
	
	if ($combine_search_results && $search_type == 'all') {
		// content and counters come from somewhere else
		continue;
	}
	
	foreach ($subtypes as $subtype) {
		unset($current_params['search_advanced_count_only']);
		if ($search_type !== 'all' && $params['subtype'] !== $subtype) {
			// only want count if doing specific search
			$current_params['search_advanced_count_only'] = true;
		}
			
		$current_params['subtype'] = $subtype;
		$current_params['type'] = $type;
		
		$view = search_get_search_view($current_params, 'list');
		if (!$view) {
			// no output view, so skip this
			continue;
		}
			
		$results = elgg_trigger_plugin_hook('search', "$type:$subtype", $current_params, NULL);
		if ($results === FALSE) {
			// someone is saying not to display these types in searches.
			continue;
		}

		if (is_array($results) && !count($results)) {
			// no results, but results searched in hook.
		} elseif (!$results) {
			// no results and not hooked.  use default type search.
			// don't change the params here, since it's really a different subtype.
			// Will be passed to elgg_get_entities().
			$results = elgg_trigger_plugin_hook('search', $type, $current_params, array());
		}

		// save result count
		$search_result_counters["item:{$type}:{$subtype}"] = $results['count'];
		
		if (!is_array($results['entities']) || empty($results['count'])) {
			// no results, so no need for any output
			continue;
		}

		if ($current_params['search_advanced_count_only'] === true) {
			// only interested in count so skip to next
			continue;
		}
			
		$results_html["item:{$type}:{$subtype}"] = elgg_view($view, [
			'results' => $results,
			'params' => $current_params,
		]);
	}
}

// add the combined content search results
if ($combine_search_results && ($search_type == 'all') && !empty($params["query"])) {
	$current_params = $params;
	$current_params['search_type'] = 'entities';
	$current_params['type'] = 'object';
	$current_params['limit'] = 20;
	
	if (array_key_exists('object', $types)) {
		// combined search results only combine objects
		elgg_push_context('combined_search');
		
		$current_params['subtype'] = $types['object'];
		$results = elgg_trigger_plugin_hook('search', $type, $current_params, array());
		if (is_array($results['entities']) && $results['count']) {
			if ($view = search_get_search_view($current_params, 'list')) {
				
				// reset count to 0 to remove the "view more" url
				$results['count'] = 0;
				
				$results_html['all:combined'] = elgg_view($view, array(
					'results' => $results,
					'params' => $current_params,
				));
			}
		}
		
		// determine menu counters
		$totals = search_advanced_get_combined_search_counters(['search_params' => $current_params]);
		if (!empty($totals)) {
			foreach ($totals as $row) {
				$search_result_counters["item:object:{$row->subtype}"] = $row->total;
			}
		}
		
		elgg_pop_context();
	}
}

// call custom searches
if (is_array($custom_types) && !empty($params["query"])) {
	foreach ($custom_types as $type) {

		$current_params = $params;
		$current_params['search_type'] = $type;
		$current_params['subtype'] = ELGG_ENTITIES_ANY_VALUE;
		$current_params['type'] = ELGG_ENTITIES_ANY_VALUE;
		
		if ($search_type != 'all' && $search_type != $type) {
			// only want count if doing specific search
			$current_params['search_advanced_count_only'] = true;
		}
		
		$results = elgg_trigger_plugin_hook('search', $type, $current_params, array());

		if ($results === FALSE) {
			// someone is saying not to display these types in searches.
			continue;
		}
		
		if (isset($results['entities']) && is_array($results['entities']) && $results['count']) {
			if ($view = search_get_search_view($current_params, 'list')) {
				$search_result_counters["search_types:" . $type] = $results['count'];
				if ($current_params['search_advanced_count_only'] !== true) {
					$results_html["search_types:$type"] = elgg_view($view, array(
						'results' => $results,
						'params' => $current_params,
					));
				}
			}
		}
		
		if (isset($results["content"])) {
			// some special case where content is provide via a hook instead of a view
			$results_html["search_types:$type"] = $results["content"];
		}
	}
}

// highlight search terms
$searched_words = search_remove_ignored_words($display_query, 'array');
$highlighted_query = '';
if (!empty($display_query)) {
	$highlighted_query = search_highlight_words($searched_words, $display_query);
}


$result_keys = array_keys($results_html);

$total = 0;
foreach ($result_keys as $key) {
	if ($key === 'all:combined') {
		$total = array_sum($search_result_counters);
		break;
	}
	$total += $search_result_counters[$key];
}

if (empty($highlighted_query)) {
	$title = elgg_echo('search_advanced:results:title', [$total, '']);
} else {
	$title = elgg_echo('search_advanced:results:title', [$total, "\"$highlighted_query\""]);
}

$results_html = elgg_trigger_plugin_hook('search_results', 'search', ['orig_results' => $results_html], $results_html);

$layout_options = [
	'types' => $types,
	'custom_types' => $custom_types,
	'params' => $params,
	'search_result_counters' => $search_result_counters,
	'body' => $results_html,
	'title' => $title
];

$result = elgg_view('search/layout', $layout_options);
if (!elgg_is_xhr()) {
	$result = elgg_view_page($page_title, $result);
}

echo $result;

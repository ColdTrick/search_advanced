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
	'search_filter' => (array) get_input('filter', []),
);

$params = elgg_trigger_plugin_hook('search_params', 'search', $params, $params);

// check and show error page
$error_vars = $vars;
$error_vars['params'] = $params;
$error_output = elgg_view('search_advanced/error', $error_vars);

if ($error_output) {
	echo $error_output;
	return;
}

$types = search_advanced_search_get_types();

// to pass the correct current search type to the views
$current_params = $params;
$current_params['search_type'] = 'entities';
// foreach through types.
// if a plugin returns FALSE for subtype ignore it.
// if a plugin returns NULL or '' for subtype, pass to generic type search function.
// if still NULL or '' or empty(array()) no results found. (== don't show??)

$combine_search_results = elgg_get_plugin_setting('combine_search_results', 'search_advanced', 'no');

// start the actual search
$search_result_counters = [];
$results_html = [];

foreach ($types as $type => $subtypes) {
	if ($type !== "object") {
		if ($type !== "user" && empty($params["query"])) {
			continue;
		}

		if ($type == "user" && empty($params["query"]) && empty($params['search_filter']['profile_fields'])) {
			continue;
		}
		// pull in default type entities with no subtypes
		$current_params['type'] = $type;
		$current_params['subtype'] = ELGG_ENTITIES_ANY_VALUE;

		unset($current_params['count']);
		if ($search_type != 'all' && $entity_type != $type) {
			// only want count if doing specific search
			$current_params['count'] = true;
		}
		
		if (($combine_search_results == 'all') && ($search_type == 'all')) {
			// content comes from somewhere else
			$current_params['count'] = true;
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
					
		if ($current_params['count'] === true) {
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
	
	foreach ($subtypes as $subtype) {
		unset($current_params['count']);
		if ($search_type !== 'all' && $params['subtype'] !== $subtype) {
			// only want count if doing specific search
			$current_params['count'] = true;
		}
		
		if (($combine_search_results !== 'no') && ($search_type == 'all')) {
			// content comes from somewhere else
			$current_params['count'] = true;
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

		if ($current_params['count'] === true) {
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
$combined_result = search_advanced_search_index_combined_search($combine_search_results, $params);
if (!empty($combined_result) && isset($combined_result['content'])) {
	$results_html['all:combined'] = $combined_result['content'];
}

// call custom searches
$custom_types = (array) elgg_trigger_plugin_hook('search_types', 'get_types', $params, []);
if (!empty($params['query'])) {
	foreach ($custom_types as $type) {

		$custom_result = search_advanced_search_index_custom_search($type, $params, $combine_search_results);
		if (empty($custom_result)) {
			continue;
		}
		if (isset($custom_result['count'])) {
			$search_result_counters["search_types:$type"] = $custom_result['count'];
		}
		
		if (isset($custom_result['content'])) {
			$results_html["search_types:$type"] = $custom_result['content'];
		}
	}
}

$result_keys = array_keys($results_html);

$total = 0;
foreach ($result_keys as $key) {
	if ($key === 'all:combined') {
		if ($combine_search_results == 'objects') {
			$total = array_sum($search_result_counters);
		} else {
			$total = $combined_result['count'];
		}
		break;
	}
	$total += $search_result_counters[$key];
}

$title = elgg_view('search/title', ['query' => $query, 'count' => $total]);

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

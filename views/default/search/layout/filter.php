<?php

if (elgg_extract('show_inline_form', $vars, true)) {
	$params = elgg_extract('search_params', $vars);
	$params['inline_form'] = true;
	
	echo elgg_view_form('search', [
		'action' => elgg_generate_url('default:search'),
		'method' => 'get',
		'disable_security' => true,
		'enable_autocomplete' => false,
	], $params);
}

echo elgg_view('page/layouts/elements/filter', ['filter_id' => 'search']);

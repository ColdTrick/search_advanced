<?php

$filter = elgg_view('search/filter', $vars);
if ($filter) {
	echo $filter;
	echo elgg_view('input/button', [
		'class' => 'search-advanced-search-sidebar-button elgg-button-submit',
		'value' => elgg_echo('search'),
	]);
}

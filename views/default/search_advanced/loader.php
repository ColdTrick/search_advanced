<?php

$url = elgg_http_add_url_query_elements(current_page_url(), ['loader' => 1]);

echo elgg_view('output/url', [
	'class' => 'hidden search-advanced-load-content',
	'href' => $url
]);

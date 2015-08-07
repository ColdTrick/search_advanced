<?php

echo elgg_view('graphics/ajax_loader', array('hidden' => false));

$url = elgg_http_add_url_query_elements(current_page_url(), array('loader' => 1));

echo elgg_format_element('script', [], "$(function() { $('.elgg-layout-one-column').load('{$url}'); });");

elgg.provide('elgg.search_advanced');

elgg.search_advanced.init_ajax_submit = function() {
	// make sure it is already loaded
	require(['elgg/spinner']);
	
	$(document).on('submit', 'form.elgg-search-header, form.elgg-form-search-advanced-search', function(event) {
		event.stopPropagation();
		event.preventDefault();
		
		$form = $(this);
		var url = elgg.normalize_url('search?loader=1&' + $form.serialize());
		
		elgg.search_advanced.ajax_load_url(url);
		
		return false;
	});
	
	$(document).on('click', 'a[href^="' + elgg.normalize_url('/search?') + '"]', function(event) {
		var current_href = $(this).attr('href');
		
		event.stopPropagation();
		event.preventDefault();
		
		// remove existing loader
		var href_parts = current_href.split('?');
		var url = '/search?loader=1&' + href_parts[1];
		
		url = elgg.normalize_url(url);
		
		elgg.search_advanced.ajax_load_url(url);
		
		return false;
	});
	
	$('.search-advanced-load-content').each(function() {
		elgg.search_advanced.ajax_load_url($(this).attr('href'));
	});
};

elgg.search_advanced.ajax_load_url = function (url) {
	require(['elgg/spinner'], function(spinner) {
		spinner.start();
				
		$('.elgg-layout').load(url, function() {
			spinner.stop();
		});
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init_ajax_submit);

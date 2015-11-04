elgg.provide('elgg.search_advanced');

elgg.search_advanced.init_ajax_submit = function() {
	// make sure it is already loaded
	require(['elgg/spinner', 'browserstate-history'], function() {
		// Bind to StateChange Event
	    History.Adapter.bind(window, 'statechange', function(){ // Note: We are using statechange instead of popstate
	        var State = History.getState(); // Note: We are using History.getState() instead of event.state
	        
	        if (elgg.search_advanced.history_pushed) {
	        	return;
	        }
	        
	        if (State.url.indexOf("search?loader=1") == -1) {
	        	document.location.href = State.url;
	    	}
	        require(['elgg/spinner'], function(spinner) {
	        	spinner.start();
	        	
	        	$('.elgg-page > .elgg-page-body > .elgg-inner').load(State.url, function() {
	        		spinner.stop();
	        		$(window).scrollTop(0);
	        	});
	        });	        	
	    });
	});
		
	$(document).on('submit', 'form.elgg-search-header, form.elgg-form-search-advanced-search', function(event) {
		event.stopPropagation();
		event.preventDefault();
		
		$form = $(this);
		var url = elgg.normalize_url('search?' + $form.serialize());
		
		elgg.search_advanced.ajax_load_url(url);
		
		return false;
	});
	
	$(document).on('click', 'a[href^="' + elgg.normalize_url('/search?') + '"]', function(event) {
		var current_href = $(this).attr('href');
		
		event.stopPropagation();
		event.preventDefault();
		
		elgg.search_advanced.ajax_load_url(current_href);
		
		return false;
	});
		
	$('.search-advanced-load-content').each(function() {
		elgg.search_advanced.ajax_load_url($(this).attr('href'));
	});
};

elgg.search_advanced.ajax_load_url = function (url) {
	require(['elgg/spinner'], function(spinner) {
		spinner.start();
	
		// remove existing loader
		
		var href_parts = url.split('?');
		var new_url = '/search?loader=1&' + href_parts[1];
		new_url = elgg.normalize_url(new_url);		
		
		$('.elgg-page > .elgg-page-body > .elgg-inner').load(new_url, function() {
			spinner.stop();
			$(window).scrollTop(0);
			
			elgg.search_advanced.history_pushed = true;
			History.pushState(null, null, new_url);
			elgg.search_advanced.history_pushed = false;
		});
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init_ajax_submit);

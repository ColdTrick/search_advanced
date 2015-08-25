elgg.provide('elgg.search_advanced');

elgg.search_advanced.init_ajax_submit = function() {
	// make sure it is already loaded
	require(['elgg/spinner']);
	
	$('form.elgg-search-header').submit(function(event) {
		event.stopPropagation();
		event.preventDefault();
		
		$form = $(this);
		
		require(['elgg/spinner'], function(spinner) {
			spinner.start();
		
			var url = elgg.normalize_url('search?loader=1&' + $form.serialize());
			$('.elgg-layout').load(url, function() {
				spinner.stop();
			});
		});
		
		return false;
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init_ajax_submit);

elgg.provide("elgg.search_advanced");

elgg.search_advanced.init_widget_search = function() {
	$(document).on('submit', '.search-advanced-widget-search-form', function(e) {
		var $target = $(this).next();
		
		var $loader = $('#elgg-widget-loader').clone();
		$loader.attr('id', '#elgg-widget-active-loader');
		$loader.removeClass('hidden');
		$target.html($loader);
		
		var data = $(this).serialize();
		data += '&widget_search=1';

		$target.load($(this).attr("action"), data).addClass("mtm");
		e.preventDefault();
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init_widget_search);
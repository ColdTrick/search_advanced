elgg.provide("elgg.search_advanced");

elgg.search_advanced.init_widget_search = function() {
	$(".search-advanced-widget-search-form").live("submit", function(e) {
		var $target = $(this).next();
		
		var $loader = $('#elgg-widget-loader').clone();
		$loader.attr('id', '#elgg-widget-active-loader');
		$loader.removeClass('hidden');
		$target.html($loader);

		$target.load($(this).attr("action"), $(this).serialize()).addClass("mtm");
		e.preventDefault();
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init_widget_search);
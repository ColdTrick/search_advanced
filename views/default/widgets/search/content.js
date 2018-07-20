define(function(require) {
	var $ = require('jquery');

	$(document).on('submit', '.search-advanced-widget-search-form', function(e) {
		var $target = $(this).next();
		
		var $loader = $('#elgg-widget-loader').clone();
		$loader.attr('id', '#elgg-widget-active-loader');
		$loader.removeClass('hidden');
		$target.html($loader);
		
		var data = $(this).serialize();
		data += '&widget_search=1';

		// @todo replace with elgg/ajax.view
		$target.load($(this).attr("action"), data).addClass("mtm");
		e.preventDefault();
	});
});

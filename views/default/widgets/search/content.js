define(['jquery', 'elgg/Ajax'], function($, Ajax) {
	var ajax = new Ajax();
	
	$(document).on('submit', '.search-advanced-widget-search-form', function(e) {
		e.preventDefault();
		
		var $target = $(this).next();

		data = ajax.objectify(this);
		data.append('widget_search', 1);
		
		ajax.path('search', {
			data : data,
			success: function (result) {
				$target.html(result).addClass("mtm");
			}
		})		
	});
});

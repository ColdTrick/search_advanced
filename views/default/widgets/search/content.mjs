import 'jquery';
import Ajax from 'elgg/Ajax';

var ajax = new Ajax();

$(document).on('submit', '.search-advanced-widget-search-form', function(e) {
	e.preventDefault();
	
	var $target = $(this).next();

	var data = ajax.objectify(this);
	data.append('widget_search', 1);
	
	ajax.path('search', {
		data: data,
		success: function (result) {
			$target.html(result).addClass("mtm");
		}
	});
});

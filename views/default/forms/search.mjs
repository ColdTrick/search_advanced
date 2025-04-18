import 'jquery';
import 'jquery-ui';
import 'elgg';

var getAutocompleteHelpers = function(query) {
	// make a clone of the original helpers
	var helpers = JSON.parse(JSON.stringify(elgg.data.search_advanced.helpers));
	var placeholder_text = '___PLACEHOLDER___';
	
	$.each(helpers, function() {
		this.content = this.content.replace(placeholder_text, query);
		this.href = this.href.replace(placeholder_text, query);
	});
	
	return helpers;
};

$(".elgg-form-search .search-input").each(function() {
	var $data = $(this).data();
	
	$(this)
	// don't navigate away from the field on tab when selecting an item
	.bind("keydown", function(event) {
		if (event.keyCode === $.ui.keyCode.TAB && $(this).data('autocomplete').menu.active) {
			event.preventDefault();
		}
	})
	.autocomplete({
		source: function( request, response ) {
			
			var helpers = getAutocompleteHelpers(request.term);
			
			$.getJSON(elgg.normalize_url("/search_advanced/autocomplete"), {
				q: request.term,
			}).done(function(data) {
				response(helpers.concat(data));
			});
			
			response(helpers);
		},
		delay: 200,
		search: function() {
			// custom minLength
			var term = this.value;
			if (term.length < 2 || term.length > 50) {
				return false;
			}

			return true;
		},
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function(event, ui) {
			if (ui.item.href) {
				document.location.href = ui.item.href;
			} else if (ui.item.type == 'placeholder') {
				return false;
			} else {
				this.value = ui.item.value;
			}
			
			return false;
		},
		autoFocus: false,
		messages: {
			noResults: '',
			results: function() {}
		},
		create: function (e) {
			$(this).prev('.ui-helper-hidden-accessible').remove();
		}
	}).data('ui-autocomplete')._renderItem = function(ul, item) {
		ul.addClass('search-autocomplete');
		
		return $("<li></li>")
			.data("item.autocomplete", item)
			.append("<span class='search-advanced-autocomplete-" + item.type + "'>" + item.content + "</span>")
			.appendTo(ul);
	};
});

$(document).on('submit', 'form.elgg-search', function(e) {
	$(".elgg-search .search-input").each(function() {
		if ($(this).data('ui-autocomplete') != undefined) {
			$(this).autocomplete("destroy");
		}
	});
});

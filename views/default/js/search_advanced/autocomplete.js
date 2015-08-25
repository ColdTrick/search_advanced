elgg.provide("elgg.search_advanced");

elgg.search_advanced.init_autocomplete = function() {
	$(".elgg-search .search-input").each(function() {
		$(this)
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function(event) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( "/search_advanced/autocomplete", {
					q: request.term
				}, response );
			},
			search: function() {
				// custom minLength
				var term = this.value;
				if ( term.length < 2){
					return false;
				}

				var search_type = $(".elgg-search input[name='entity_type']").val();
				if( search_type && search_type != "user" && search_type != "group"){
					return false;
				}
				
				return true;
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				if (ui.item.href) {
					document.location.href = ui.item.href;
				} else if(ui.item.type == "placeholder"){
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
		}).data( "ui-autocomplete" )._renderItem = function(ul, item) {
			
			return $("<li></li>")
			.data("item.autocomplete", item)
			.append("<a class='search-advanced-autocomplete-" + item.type + "'>" + item.content + "</a>")
			.appendTo(ul);
		};
	});

	$("form.elgg-search").live("submit", function(e) {
		$(".elgg-search .search-input").each(function() {
			$(this).autocomplete("destroy");
		});
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init_autocomplete);

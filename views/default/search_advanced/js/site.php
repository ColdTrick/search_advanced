<?php ?>
//<script>

elgg.provide("elgg.search_advanced");

elgg.search_advanced.init = function() {
	$(".elgg-search .search-input")
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
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
					return false
				}
				return true;
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				if(ui.item.href){
					document.location.href = ui.item.href;
				} else {
					this.value = ui.item.value;
				}
				return false;
			},
			autoFocus: true
		}).data( "autocomplete" )._renderItem = function( ul, item ) {
			var list_body = "";
			list_body = item.content;
			
		
			return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( "<a>" + list_body + "</a>" )
			.appendTo( ul );
		};
}


elgg.register_hook_handler('init', 'system', elgg.search_advanced.init);
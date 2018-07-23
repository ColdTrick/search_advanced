define(function(require) {
	var $ = require('jquery');
	
	$(document).on('click', '.elgg-menu-search-type-selection .elgg-child-menu a', function(e) {
		e.preventDefault();
		console.log($(this).parent());
		if ($(this).parent().hasClass('elgg-menu-item--selected')) {
			return;
		}
		
		$('.elgg-menu-search-type-selection > li > a').html($(this).html());
		
		var $form = $(this).closest('.elgg-form-search');
		console.log($form);
		console.log($(this).data());
		$form.find("input[name='search_type']").val($(this).data().searchType);
		$form.find("input[name='entity_type']").val($(this).data().entityType);
		$form.find("input[name='entity_subtype']").val($(this).data().entitySubtype);

	});
});

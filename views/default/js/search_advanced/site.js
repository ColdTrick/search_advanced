elgg.provide("elgg.search_advanced");

elgg.search_advanced.init = function() {
	
	$(document).on('click', '.search-advanced-search-sidebar-button', function() {
		$('.elgg-form-search-advanced-search').submit();
	});
	
	$(document).on('submit', 'form.elgg-form-search-advanced-search', function(event) {
		var $sidebar_input = $('.elgg-sidebar [name^="filter"]');
		if (!$sidebar_input.length) {
			return;
		}
		
		// fix select, jquery clone doesn't keep value for select
		var $clone = $sidebar_input.clone();
		var $clone_selects = $clone.filter('select');
		
		$sidebar_input.filter('select').each(function(index, elem) {
			$clone_selects.eq(index).val($(elem).val());
		});
		
		$(this).append($clone.hide());
	});
	
	$(document).on('keypress', '.elgg-sidebar [name^="filter"]', function(event) {
		if (event.keyCode !== 13) {
			return;
		}
		$('.elgg-form-search-advanced-search').submit();
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init);

elgg.provide("elgg.search_advanced");

elgg.search_advanced.init = function() {
	$('.elgg-menu-search-list > li > a').live('click', function(e) {
		var $child_menu = $(this).next('.elgg-child-menu');
		if ($child_menu.length) {
			// hide other submenus
			$('.elgg-menu-search-list .elgg-child-menu').hide();
			
			// show or hide submenu
			$child_menu.toggle();
		
			e.stopPropagation();
			e.preventDefault();
		}
	});
	
	$(document).click(function() {
		$('.elgg-menu-search-list .elgg-child-menu').hide();
	});
	
	$(document).on('change', 'select.search-advanced-search-types', function() {
		var url = $(this).val();
		if (elgg.search_advanced.ajax_load_url == undefined) {
			document.location = url;
		} else {
			elgg.search_advanced.ajax_load_url(url);
		}
	});

	$(document).on('click', '.search-advanced-search-sidebar-button', function() {
		$('.elgg-form-search-advanced-search').submit();
	});
	
	$(document).on('submit', 'form.elgg-form-search-advanced-search', function(event) {
		var $sidebar_input = $('.elgg-sidebar [name^="filter"]');
		if (!$sidebar_input.length) {
			return;
		}
		
		var href = elgg.normalize_url('search?' + $sidebar_input.serialize());
		$(this).append($sidebar_input.clone().hide());
	});
	
	$(document).on('keypress', '.elgg-sidebar [name^="filter"]', function(event) {
		if (event.keyCode !== 13) {
			return;
		}
		$('.elgg-form-search-advanced-search').submit();
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init);

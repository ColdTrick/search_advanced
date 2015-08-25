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
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init);

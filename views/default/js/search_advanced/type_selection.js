elgg.provide("elgg.search_advanced");

elgg.search_advanced.init_type_selection = function() {
	$(document).on('click', '.search-advanced-type-selection > li > a', function(e) {
		$(this).next().show();
		e.preventDefault();
		e.stopPropagation();
	});

	$(document).on('click', '.search-advanced-type-selection-dropdown', function(e) {
		e.stopPropagation();
	});

	$(document).on('click', '.search-advanced-type-selection-dropdown a', function(e) {
		$(".search-advanced-type-selection > li > a").html($(this).html());

		$(".elgg-search input[name='search_type']").attr("disabled", "disabled");
		$(".elgg-search input[name='entity_type']").attr("disabled", "disabled").val("");
		$(".elgg-search input[name='entity_subtype']").attr("disabled", "disabled").val("");
		
		var rel = $(this).attr("rel");
		
		if (rel) {
			
			var input_vals = rel.split(" ");
			
			if (input_vals[0]) {

				if (input_vals[0] == "object" || input_vals[0] == "user" || input_vals[0] == "group") { 
					$(".elgg-search input[name='search_type']").val("entities").removeAttr("disabled");
					$(".elgg-search input[name='entity_type']").val(input_vals[0]).removeAttr("disabled");
				} else {
					$(".elgg-search input[name='search_type']").val(input_vals[0]).removeAttr("disabled");
				}
			}

			if (input_vals[1]) {
				$(".elgg-search input[name='entity_subtype']").val(input_vals[1]).removeAttr("disabled");
			}
		}
		
		$(".search-advanced-type-selection-dropdown").hide();
		
		var $form = $(this).parents('form');
		var q = $form.find('[name="q"]').val();
		if (q) {
			$form.submit();
		}
	});

	$(document).on('click', function() {
		$(".search-advanced-type-selection-dropdown").hide();
	});
};

elgg.register_hook_handler('init', 'system', elgg.search_advanced.init_type_selection);

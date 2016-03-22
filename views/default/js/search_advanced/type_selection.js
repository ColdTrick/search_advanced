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

		$(".elgg-search input[name='search_type']").prop("disabled", true).val("all");
		$(".elgg-search input[name='entity_type']").prop("disabled", true).val("");
		$(".elgg-search input[name='entity_subtype']").prop("disabled", true).val("");
		
		var rel = $(this).attr("rel");
		
		if (rel) {
			
			var input_vals = rel.split(" ");
			
			if (input_vals[0]) {

				if (input_vals[0] == "object" || input_vals[0] == "user" || input_vals[0] == "group") { 
					$(".elgg-search input[name='search_type']").val("entities").prop("disabled", false);
					$(".elgg-search input[name='entity_type']").val(input_vals[0]).prop("disabled", false);
				} else {
					$(".elgg-search input[name='search_type']").val(input_vals[0]).prop("disabled", false);
				}
			}

			if (input_vals[1]) {
				$(".elgg-search input[name='entity_subtype']").val(input_vals[1]).prop("disabled", false);
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

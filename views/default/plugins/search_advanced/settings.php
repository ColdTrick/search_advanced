<?php
	$yesno_options = array(
			"yes" => elgg_echo("option:yes"),
			"no" => elgg_echo("option:no")
	);
	$noyes_options = array_reverse($yesno_options);

	echo "<label>" . elgg_echo('search_advanced:settings:combine_search_results') . "</label> ";
	echo elgg_view("input/dropdown", array("name" => "params[combine_search_results]", "options_values" => $noyes_options, "value" => $vars['entity']->combine_search_results));
	echo "<div class='elgg-subtext'>" . elgg_echo('search_advanced:settings:combine_search_results:info') . "</div>";
	
<?php
/**
 * All page handlers are bundled here
 */

/**
 * Handles search pages
 *
 * @param array $page page segments
 *
 * @return boolean
 */
function search_advanced_page_handler($page){
	switch ($page[0]) {
		case "autocomplete":
			include_once(dirname(dirname(__FILE__)) . "/procedures/autocomplete.php");
			return true;
	}
	
	return false;
}

/**
 * Handles search advanced pages
 *
 * @param array $page page segments
 *
 * @return boolean
 */
function search_advanced_search_page_handler($page){
	// if there is no q set, we"re being called from a legacy installation
	// it expects a search by tags.
	// actually it doesn"t, but maybe it should.
	// maintain backward compatibility
	if (!get_input("q", get_input("tag", NULL)) && isset($page[0])) {
		set_input("q", $page[0]);
		//set_input("search_type", "tags");
	}
	
	// as there is no tags search any more, replace it with ALL search
	if (get_input("search_type") == "tags") {
		set_input("search_type", "all");
	}
	
	include_once(dirname(dirname(__FILE__)) . "/pages/search/index.php");
	return true;
}

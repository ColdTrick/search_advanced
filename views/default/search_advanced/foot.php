<?php
/**
 * The view prepends the page/elements/foot view to do the plugin settings check as late as possible
 */

if (elgg_get_plugin_setting('search_with_loader', 'search_advanced') == 'yes') {
	// @todo this could be moved to the global site js, but requires a simplecache refresh on plugin settings change
	elgg_require_js('search_advanced/ajax_submit');
}
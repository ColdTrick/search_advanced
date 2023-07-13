<?php

namespace ColdTrick\SearchAdvanced\Plugins;

/**
 * Event listeners for the OpenSearch plugin
 */
class OpenSearch {
	
	/**
	 * Unregister prevented search type in admin context
	 *
	 * @param \Elgg\Event $event 'index_entity_type_subtypes', 'opensearch'
	 *
	 * @return null|array
	 */
	public static function unregisterPreventedSearchTypes(\Elgg\Event $event): ?array {
		if (!elgg_in_context('admin')) {
			return null;
		}
		
		$type_subtypes = $event->getValue();
		foreach ($type_subtypes as $type => $subtypes) {
			if (empty($subtypes) || !is_array($subtypes)) {
				continue;
			}
			
			foreach ($subtypes as $index => $subtype) {
				if (elgg_get_plugin_setting("{$type}_{$subtype}_searchable", 'search_advanced', true)) {
					continue;
				}
				
				unset($type_subtypes[$type][$index]);
			}
		}
		
		return $type_subtypes;
	}
}

<?php

namespace ColdTrick\SearchAdvanced;

/**
 * Plugin settings
 */
class Settings {
	
	/**
	 * Modifies the value of the array type setting
	 *
	 * @param \Elgg\Event $event 'setting', 'plugin'
	 *
	 * @return array
	 */
	public static function saveArrayTypeSetting(\Elgg\Event $event) {
		
		$plugin = $event->getParam('plugin');
		if (!$plugin instanceof \ElggPlugin || $plugin->getID() !== 'search_advanced') {
			return;
		}
		
		$value = $event->getParam('value');
		if (!is_array($value)) {
			return;
		}
		
		return json_encode($value);
	}
	
	/**
	 * Disables searchable capabilities for configured type/subtypes
	 *
	 * @param \Elgg\Event $event 'ready', 'system'
	 *
	 * @return array
	 */
	public static function disableSearchables(\Elgg\Event $event) {
		if (elgg_in_context('admin')) {
			// admin pages should always be able to list/view/configure original searchable type/subtypes
			return;
		}
		
		$searchable_entities = elgg_entity_types_with_capability('searchable');
		foreach ($searchable_entities as $type => $subtypes) {
			foreach ($subtypes as $subtype) {
				if (!elgg_get_plugin_setting("{$type}_{$subtype}_searchable", 'search_advanced', true)) {
					elgg_entity_disable_capability($type, $subtype, 'searchable');
				}
			}
		}
	}
}

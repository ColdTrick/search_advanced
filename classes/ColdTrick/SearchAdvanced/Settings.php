<?php

namespace ColdTrick\SearchAdvanced;

class Settings {
	
	/**
	 * Modifies the value of the array type setting
	 *
	 * @param \Elgg\Hook $hook 'setting', 'plugin'
	 *
	 * @return array
	 */
	public static function saveArrayTypeSetting(\Elgg\Hook $hook) {
		
		$plugin = $hook->getParam('plugin');
		if (!$plugin instanceof \ElggPlugin || $plugin->getID() !== 'search_advanced') {
			return;
		}
		
		$value = $hook->getParam('value');
		if (!is_array($value)) {
			return;
		}
		
		return json_encode($value);
	}
	
	/**
	 * Flushes cache if search_with_loader setting changed
	 *
	 * @param \Elgg\Hook $hook 'setting', 'plugin'
	 *
	 * @return array
	 */
	public static function flushCache(\Elgg\Hook $hook) {
		
		$plugin = $hook->getParam('plugin');
		if (!$plugin instanceof \ElggPlugin || $plugin->getID() !== 'search_advanced') {
			return;
		}
		
		if ($hook->getParam('name') !== 'search_with_loader') {
			return;
		}
		
		if ($hook->getParam('value') !== $plugin->search_with_loader) {
			elgg_invalidate_simplecache();
		}
	}
}

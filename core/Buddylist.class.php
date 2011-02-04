<?php

class Buddylist {

	/**
	 * @name: is_online
	 * @description: Returns null when online status is unknown, 1 when buddy is online, 0 when buddy is offline
	 */
	public static function is_online($name) {
		global $chatBot;
	
		$buddy = $chatBot->get_buddy($name);
		return ($buddy === null ? null : $buddy['online']);
    }
	
	public static function add($name, $type) {
		global $chatBot;

		$uid = $chatBot->get_uid($name);
		if ($uid === false || $type === null || $type == '') {
			return false;
		} else {
			if (!isset($chatBot->buddyList[$uid])) {
				Logger::log('debug', "Buddy", "$name buddy added");
				$chatBot->buddy_add($uid);
			}
			
			if (!isset($chatBot->buddyList[$uid]['types'][$type])) {
				$chatBot->buddyList[$uid]['types'][$type] = 1;
				Logger::log('debug', "Buddy", "$name buddy added (type: $type)");
			}
			
			return true;
		}
	}
	
	public static function remove($name, $type = '') {
		global $chatBot;

		$uid = $chatBot->get_uid($name);
		if ($uid === false) {
			return false;
		} else if (isset($chatBot->buddyList[$uid])) {
			if (isset($chatBot->buddyList[$uid]['types'][$type])) {
				unset($chatBot->buddyList[$uid]['types'][$type]);
				Logger::log('debug', "Buddy", "$name buddy type removed (type: $type)");
			}

			if (count($chatBot->buddyList[$uid]['types']) == 0) {
				unset($chatBot->buddyList[$uid]);
				Logger::log('debug', "Buddy", "$name buddy removed");
				$chatBot->buddy_remove($uid);
			}
			
			return true;
		} else {
			return false;
		}
	}
}

?>
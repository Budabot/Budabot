<?php

class Buddylist {

	/** @Inject */
	public $chatBot;
	
	/** @Logger */
	public $logger;

	/**
	 * @name: is_online
	 * @description: Returns null when online status is unknown, 1 when buddy is online, 0 when buddy is offline
	 */
	public function is_online($name) {
		$buddy = $this->get_buddy($name);
		return ($buddy === null ? null : $buddy['online']);
    }
	
	public function get_buddy($name) {
		$uid = $this->chatBot->get_uid($name);
		if ($uid === false || !isset($this->chatBot->buddyList[$uid])) {
			return null;
		} else {
			return $this->chatBot->buddyList[$uid];
		}
    }
	
	public function add($name, $type) {
		$uid = $this->chatBot->get_uid($name);
		if ($uid === false || $type === null || $type == '') {
			return false;
		} else {
			if (!isset($this->chatBot->buddyList[$uid])) {
				$this->logger->log('debug', "$name buddy added");
				$this->chatBot->buddy_add($uid);
			}
			
			if (!isset($this->chatBot->buddyList[$uid]['types'][$type])) {
				$this->chatBot->buddyList[$uid]['types'][$type] = 1;
				$this->logger->log('debug', "$name buddy added (type: $type)");
			}
			
			return true;
		}
	}
	
	public function remove($name, $type = '') {
		$uid = $this->chatBot->get_uid($name);
		if ($uid === false) {
			return false;
		} else if (isset($this->chatBot->buddyList[$uid])) {
			if (isset($this->chatBot->buddyList[$uid]['types'][$type])) {
				unset($this->chatBot->buddyList[$uid]['types'][$type]);
				$this->logger->log('debug', "$name buddy type removed (type: $type)");
			}

			if (count($this->chatBot->buddyList[$uid]['types']) == 0) {
				unset($this->chatBot->buddyList[$uid]);
				$this->logger->log('debug', "$name buddy removed");
				$this->chatBot->buddy_remove($uid);
			}
			
			return true;
		} else {
			return false;
		}
	}
}

?>
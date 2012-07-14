<?php

function cmp($a, $b) {
    if ($a->time == $b->time) {
        return 0;
    }
    return ($a->time < $b->time) ? -1 : 1;
}

class Timer {
	private $timerEvents = array();

	public function executeTimerEvents() {
		while (count($this->timerEvents) > 0 && $this->timerEvents[0]->time <= time()) {
			$timerEvent = array_shift($this->timerEvents);
			call_user_func($timerEvent->callback, $timerEvent->time, $timerEvent->callbackParam);
		}
	}

	public function addTimerEvent($groupId, $callback, $callbackParam, $time) {
		$timerEvent = new stdClass;
		$timerEvent->groupId = $groupId;
		$timerEvent->callback = $callback;
		$timerEvent->callbackParam = $callbackParam;
		$timerEvent->time = $time;

		$this->timerEvents []= $timerEvent;
		usort($this->timerEvents, "cmp");
	}
}

?>

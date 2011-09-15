<?php

function cmp($a, $b) {
    if ($a->time == $b->time) {
        return 0;
    }
    return ($a->time < $b->time) ? -1 : 1;
}

class Timer {
	private static $timerEvents = array();
	
	public static function executeTimerEvents() {
		while (count(Timer::$timerEvents) > 0 && Timer::$timerEvents[0]->time <= time()) {
			$timerEvent = array_shift(Timer::$timerEvents);
			call_user_func($timerEvent->callback, $timerEvent->time, $timerEvent->callbackParam);
		}
	}
	
	public static function addTimerEvent($groupId, $callback, $callbackParam, $time) {
		$timerEvent = new stdClass;
		$timerEvent->groupId = $groupId;
		$timerEvent->callback = $callback;
		$timerEvent->callbackParam = $callbackParam;
		$timerEvent->time = $time;
		
		Timer::$timerEvents []= $timerEvent;
		usort(Timer::$timerEvents, "cmp");
	}
}

?>

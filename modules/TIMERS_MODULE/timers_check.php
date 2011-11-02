<?php

//Check if at least one timer is running
if (count($chatBot->data["timers"]) == 0) {
	return;
}

forEach ($chatBot->data["timers"] as $key => $timer) {
	$msg = "";

	$tleft = $timer->timer - time();
	$set_time = $timer->settime;
	$name = $timer->name;
	$owner = $timer->owner;
	$mode = $timer->mode;

	if ($tleft >= 3599 && $tleft < 3601 && ((time() - $set_time) >= 30)) {
		if ($name == $owner) {
			$msg = "Reminder: Timer has <highlight>1 hour<end> left [set by <highlight>$owner<end>]";
		} else {
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1 hour<end> left [set by <highlight>$owner<end>]";
		}
	} else if ($tleft >= 899 && $tleft < 901 && ((time() - $set_time) >= 30)) {
		if ($name == $owner) {
			$msg = "Reminder: Timer has <highlight>15 minutes<end> left [set by <highlight>$owner<end>]";
		} else {
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>15 minutes<end> left [set by <highlight>$owner<end>]";
		}
	} else if ($tleft >= 59 && $tleft < 61 && ((time() - $set_time) >= 30)) {
		if ($name == $owner) {
			$msg = "Reminder: Timer has <highlight>1 minute<end> left [set by <highlight>$owner<end>]";
		} else {
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1 minute<end> left [set by <highlight>$owner<end>]";
		}
	} else if ($tleft <= 0) {
		if ($tleft >= -600) {
			if ($name == $owner) {
				$msg = "<highlight>$owner<end> your timer has gone off";
			} else {
				$msg = "<highlight>$owner<end> your timer named <highlight>$name<end> has gone off";
			}
		}
	
		Timer::remove_timer($key);
		if ($timer->callback == 'repeating') {
			Timer::add_timer($name, $owner, $mode, $timer->callback_param + time(), $timer->callback, $timer->callback_param);
		}
	}

	if ('' != $msg) {
		if ('msg' == $mode) {
			$chatBot->send($msg, $owner);
		} else {
			$chatBot->send($msg, $mode);
		}
	}
}

?>
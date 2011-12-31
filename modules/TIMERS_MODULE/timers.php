<?php

$accessLevel = $chatBot->getInstance('accessLevel');

if (preg_match("/^timers view (.+)$/i", $message, $arr)) {
	$timer_name = trim($arr[1]);
	
	$timer = Timer::get($timer_name);
	if ($timer == null) {
		$msg = "Could not find timer named <highlight>$timer_name<end>.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$time_left = Util::unixtime_to_readable($timer->timer - time());
	$name = $timer->name;

	$msg = "Timer <highlight>$name<end> has <highlight>$time_left<end> left.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^(timers|timers add) ([0-9]+)$/i", $message, $arr) || preg_match("/^(timers|timers add) ([0-9]+) (.+)$/i", $message, $arr)) {
	
	if (isset($arr[3])) {
		$timer_name = trim($arr[3]);
	} else {
		$timer_name = $sender;
	}
	if ($arr[2] < 1) {
		$msg = "You must enter a valid time parameter.";
		$chatBot->send($msg, $sendto);
		return;
	} else {
		$run_time = $arr[2] * 60;
	}

	if (Timer::get($timer_name) != null) {
		$msg = "A timer named <highlight>$timer_name<end> is already running.";
		$chatBot->send($msg, $sendto);
		return;
	}

    $timer = time() + $run_time;

	Timer::add($timer_name, $sender, $type, $timer);

	$timerset = Util::unixtime_to_readable($run_time);
	$msg = "Timer <highlight>$timer_name<end> has been set for $timerset.";
		
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^timers (rem|del) (.+)$/i", $message, $arr)) {
	$timer_name = strtolower($arr[2]);
	
	$timer = Timer::get($timer_name);
	if ($timer == null) {
		$msg = "Could not find a timer named <highlight>$timer_name<end>.";
	} else if ($timer->owner != $sender && !$accessLevel->checkAccess($sender, "rl")) {
		$msg = "You don't have the required access level (raidleader) to remove this timer.";
	} else {
		Timer::remove($timer_name);
		$msg = "Removed timer <highlight>$timer_name<end>.";
	}

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^(timers add|timers) ([a-z0-9]+) (.+)$/i", $message, $arr) ||
		preg_match("/^(timers add|timers) ([a-z0-9]+)$/i", $message, $arr2)) {

	if (isset($arr2)) {
		$time_string = $arr2[2];
		$timer_name = $sender;
	} else {
		$time_string = $arr[2];
		$timer_name = $arr[3];
	}
	
	if (Timer::get($timer_name) != null) {
		$msg = "A timer named <highlight>$timer_name<end> is already running.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$run_time = Util::parseTime($time_string);

	if ($run_time < 1) {
		$msg = "You must enter a valid time parameter.";
		$chatBot->send($msg, $sendto);
		return;
	}

    $timer = time() + $run_time;

	Timer::add($timer_name, $sender, $type, $timer);

	$timerset = Util::unixtime_to_readable($run_time);
	$msg = "Timer <highlight>$timer_name<end> has been set for $timerset.";
		
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^timers$/i", $message, $arr)) {
	$timers = Timer::getAllTimers();
	if (count($timers) == 0) {
		$msg = "No timers currently running.";
	    $chatBot->send($msg, $sendto);
	    return;
	}

	$blob = "<header> :::::: Timers Currently Running :::::: <end>\n\n";
	forEach ($timers as $timer) {
		$time_left = Util::unixtime_to_readable($timer->timer - time());
		$name = $timer->name;
		$owner = $timer->owner;

		$remove_link = Text::make_chatcmd("Remove", "/tell <myname> timers rem $name");

		$repeatingInfo = '';
		if ($timer->callback == 'repeating') {
			$repeatingTimeString = Util::unixtime_to_readable($timer->callback_param);
			$repeatingInfo = " (Repeats every $repeatingTimeString)";
		}

		$blob .= "Name: <highlight>$name<end> {$remove_link}\n";
		$blob .= "Time left: <highlight>$time_left<end> $repeatingInfo\n";
		$blob .= "Set by: <highlight>$owner<end>\n\n";
	}
	$msg = Text::make_blob("Timers currently running", $blob);

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
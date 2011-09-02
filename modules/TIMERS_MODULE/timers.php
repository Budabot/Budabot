<?php

$msg = "";
if (preg_match("/^(timers|timers add) ([0-9]+)$/i", $message, $arr) || preg_match("/^(timers|timers add) ([0-9]+) (.+)$/i", $message, $arr)) {
	if ($arr[3] == '') {
		$timer_name = 'PrimTimer';
	} else {
		$timer_name = trim($arr[3]);
	}
	if ($arr[2] < 1) {
		$syntax_error = true;
		return;
	} else {
		$run_time = $arr[2] * 60;
	}

	forEach ($chatBot->data["timers"] as $timer) {
		if ($timer->name == $timer_name) {
			$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";
			$chatBot->send($msg, $sendto);
			return;
		}
	}

    $timer = time() + $run_time;

	Timer::add_timer($timer_name, $sender, $type, $timer);

	$timerset = Util::unixtime_to_readable($run_time);
	$msg = "Timer has been set for $timerset.";
		
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^timers (rem|del) (.+)$/i", $message, $arr)) {
	$timer_name = strtolower($arr[2]);
	
	forEach ($chatBot->data["timers"] as $key => $timer) {
		$name = $timer->name;
		$owner = $timer->owner;

		if (strtolower($name) == $timer_name) {
			if ($owner == $sender || AccessLevel::check_access($sender, "guildadmin")) {
				Timer::remove_timer($key);
					
			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;
			} else {
				$msg = "You don't have the required access level (guildadmin) to remove this timer.";
				break;
			}
		}
	}

	if (!$msg) {
		$msg = "A timer with this name is not running.";
	}

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^(timers add|timers) ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?)$/i", $message, $arr) ||
		preg_match("/^(timers add|timers) ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?) (.+)$/i", $message, $arr2)) {

	if ($arr) {
		$timer_name = 'PrimTimer';
		$time_string = $arr[2];
	} else if ($arr2) {
		$timer_name = trim($arr2[count($arr2) - 1]);
		$time_string = $arr2[2];
	}
	
	forEach ($chatBot->data["timers"] as $timer) {
		if ($timer->name == $timer_name) {
			$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";
			$chatBot->send($msg, $sendto);
			return;
		}
	}

	$run_time = Util::parseTime($time_string);

	if ($run_time == 0) {
		$msg = "Your timer must be longer than 0 seconds.";
		$chatBot->send($msg, $sendto);
		return;
	}

    $timer = time() + $run_time;

	Timer::add_timer($timer_name, $sender, $type, $timer);

	$timerset = Util::unixtime_to_readable($run_time);
	$msg = "Timer has been set for $timerset.";
		
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^timers$/i", $message, $arr)) {
	$num_timers = count($chatBot->data["timers"]);
	if ($num_timers == 0) {
		$msg = "No Timers running atm.";
	    $chatBot->send($msg, $sendto);
	    return;
	}

  	if ($num_timers <= $chatBot->settings["timers_window"]) {
		forEach ($chatBot->data["timers"] as $timer) {
			$time_left = Util::unixtime_to_readable($timer->timer - time());
			$name = $timer->name;
			$owner = $timer->owner;
			$mode = $timer->mode;

			if ($name == "PrimTimer") {
				$msg .= "\n Timer has <highlight>$time_left<end> left [set by <highlight>$owner<end>]";
			} else {
				$msg .= "\n Timer <highlight>$name<end> has <highlight>$time_left<end> left [set by <highlight>$owner<end>]";  	
			}
		}
		if ($msg == "") {
			$msg = "No Timers running atm.";
		} else {
		  	$msg = "Timers currently running:".$msg;
		}
	} else {
		forEach ($chatBot->data["timers"] as $timer) {
			$time_left = Util::unixtime_to_readable($timer->timer - time());
			$name = $timer->name;
			$owner = $timer->owner;
			$mode = $timer->mode;
			
			$remove_link = Text::make_chatcmd("Remove", "/tell <myname> timers rem $name");

			$list .= "Timername: <highlight>$name<end> {$remove_link}\n";
			$list .= "Timeleft: <highlight>$time_left<end>\n";
			$list .= "Set by: <highlight>$owner<end>\n\n";
		}
		if ($list == "") {
			$msg = "No Timers running atm.";
		} else {
			$list = "<header> :::::: Timers Currently Running :::::: <end>\n\n".$list;
		  	$msg = Text::make_blob("Timers currently running", $list);
		}
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
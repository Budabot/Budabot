<?php

$msg = "";
if (preg_match("/^timers ([0-9]+)$/i", $message, $arr) || preg_match("/^timers ([0-9]+) (.+)$/i", $message, $arr) || preg_match("/^timers add ([0-9]+)$/i", $message, $arr2) || preg_match("/^timers add ([0-9]+) (.+)$/i", $message, $arr2)) {
	// Checks if timer is either !timers add or !timers. I want to try and compact this code tighter as well by changing $arr2 to $arr and see how it works out.
	if ($arr) {
		if ($arr[2] == '') {
			$timer_name = 'PrimTimer';
		} else {
			$timer_name = trim($arr[2]);
		}
		if ($arr[1] < 1) {
			$syntax_error = true;
			return;
		} else {
			$run_time = $arr[1] * 60;
		}
	} else {
		if ($arr2[2] == '') {	
			$timer_name = 'PrimTimer';
		} else {
			$timer_name = trim($arr2[2]);
		}
		if ($arr2[1] < 1) {
			$syntax_error = true;
			return;
		} else {
			$run_time = $arr2[1] * 60;
		}
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
			if ($owner == $sender) {
				Timer::remove_timer($key);
					
			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;
			} else if (AccessLevel::check_access($sender, "guildadmin")) {
				Timer::remove_timer($key);

			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;
			} else {
				$msg = "You don't have the rights to remove this timer.";
			}
		}
	}

	if (!$msg) {
		$msg = "A timer with this name is not running.";
	}

    $chatBot->send($msg, $sendto);
/*  Reordered to try and work with current code sequence. EVERYTHING works except when days is used
	!timers add 1d and !timers add 1d <name>. Will try to figure out the rest when I wake up!
	Only day doesn't work correctly, it sets it as primtimer no matter what when days is mentioned!
*/
} else if (preg_match("/^timers add ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?) (.+)$/i", $message, $arr2) ||
		preg_match("/^timers add ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?)$/i", $message, $arr) ||
		preg_match("/^timers ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?) (.+)$/i", $message, $arr3) ||
		preg_match("/^timers ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?)$/i", $message, $arr4)) {
	// Checks which array is being used 
	if ($arr) {
		$timer_name = 'PrimTimer';
		$time_string = $arr[1];
	} else if ($arr2) {
		$arr = $arr2;
		$last_item = count($arr2);
		$timer_name = trim($arr2[$last_item - 1]);
		$time_string = $arr[1];
	} else if ($arr3) {
		$arr = $arr3;
		$last_item = count($arr3);
		$timer_name = trim($arr3[$last_item - 1]);
		$time_string = $arr[1];
	} else {
		$arr = $arr4;
		$timer_name = 'PrimTimer';
		$time_string = $arr[1];
	}
	
	forEach ($chatBot->data["timers"] as $timer) {
		if ($timer->name == $timer_name) {
			$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";
			$chatBot->send($msg, $sendto);
			return;
		}
	}

	$run_time = 0;

	if (preg_match("/([0-9]+)(d|day|days)/i", $time_string, $day)) {
		if ($day[1] < 1) {
			$syntax_error = true;
		    return;
		}
		$run_time += $day[1] * 86400;
	}

	if (preg_match("/([0-9]+)(h|hr|hrs)/i", $time_string, $hours)) {
		if ($hours[1] < 1) {
			$syntax_error = true;
		    return;		  	
		}
		$run_time += $hours[1] * 3600;
	}

	if (preg_match("/([0-9]+)(m|min|mins)/i", $time_string, $mins)) {
		if ($mins[1] < 1) {
			$syntax_error = true;
		    return;		  	
		}
		$run_time += $mins[1] * 60;
	}

	if (preg_match("/([0-9]+)(s|sec|secs)/i", $time_string, $secs)) {
		if ($secs[1] < 1) {
			$syntax_error = true;
		    return;		  	
		}
		$run_time += $secs[1];
	}

	if ($run_time == 0) {
		$syntax_error = true;
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

			$list .= "Timername: <highlight>$name<end>\n";
			$list .= "Timeleft: <highlight>$time_left<end>\n";
			$list .= "Set by: <highlight>$owner<end>\n\n";
		}
		if ($list == "") {
			$msg = "No Timers running atm.";
		} else {
			$list = "<header>::::: Currently running Timers :::::<end>\n\n".$list;
		  	$msg = Text::make_blob("Timers currently running", $list);
		}
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
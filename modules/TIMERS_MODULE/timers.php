<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Set and show Timers 
   ** Version: 1.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 26.12.2005
   ** Date(last modified): 30.01.2006
   ** 
   ** Copyright (C) 2005, 2006, 2007 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */

$msg = "";
if (preg_match("/^timers ([0-9]+)$/i", $message, $arr) || preg_match("/^timers ([0-9]+) (.+)$/i", $message, $arr)) {
	if ($arr[2] == '') {
		$timer_name = 'PrimTimer';
	} else {
		$timer_name = trim($arr[2]);
	}
	
	forEach ($chatBot->data["timers"] as $timer) {
		if ($timer->name == $timer_name) {
			$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";
			$chatBot->send($msg, $sendto);
			return;
		}
	}

	if ($arr[1] < 1) {
		$msg = "No valid time specified!";
        $chatBot->send($msg, $sendto);
	    return;
	}

	$run_time = $arr[1] * 60;
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
				Timer::remove_timer($key, $name, $sender);
					
			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;
			} else if (($chatBot->guildmembers[$sender] <= $chatBot->settings['guild_admin_level']) || isset($chatBot->admins[$charid])) {
				Timer::remove_timer($key, $name, $owner);

			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;
			} else {
				$msg = "You don't have the right to remove this timer.";
			}
		}
	}

	if (!$msg) {
		$msg = "A timer with this name is not running.";
	}

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^timers ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?)$/i", $message, $arr) ||
		preg_match("/^timers ((([0-9]+)(d|day|days))?.?(([0-9]+)(h|hr|hrs))?.?(([0-9]+)(m|min|mins))?.?(([0-9]+)(s|sec|secs))?) (.+)$/i", $message, $arr2)) {

	if ($arr2) {
		$arr = $arr2;
		$last_item = count($arr);
		$timer_name = trim($arr[$last_item - 1]);
	} else {
		$timer_name = 'PrimTimer';
	}
	
	$time_string = $arr[1];
	
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
			$msg = "No valid time specified!";
		    $chatBot->send($msg, $sendto);
		    return;
		}
		$run_time += $day[1] * 86400;
	}

	if (preg_match("/([0-9]+)(h|hr|hrs)/i", $time_string, $hours)) {
		if ($hours[1] < 1) {
			$msg = "No valid time specified!";
		    $chatBot->send($msg, $sendto);
		    return;		  	
		}
		$run_time += $hours[1] * 3600;
	}

	if (preg_match("/([0-9]+)(m|min|mins)/i", $time_string, $mins)) {
		if ($mins[1] < 1) {
			$msg = "No valid time specified!";
		    $chatBot->send($msg, $sendto);
		    return;		  	
		}
		$run_time += $mins[1] * 60;
	}

	if (preg_match("/([0-9]+)(s|sec|secs)/i", $time_string, $secs)) {
		if ($secs[1] < 1) {
			$msg = "No valid time specified!";
		    $chatBot->send($msg, $sendto);
		    return;		  	
		}
		$run_time += $secs[1];
	}

	if ($run_time == 0) {
	  	$msg = "No valid Time specified!";
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

			$list .= "Timername: <highlight>$name<end>\n";
			$list .= "Timeleft: <highlight>$time_left<end>\n";
			$list .= "Set by: <highlight>$owner<end>\n\n";
		}
		if ($list == "") {
			$msg = "No Timers running atm.";
		} else {
			$list = "<header>::::: Currently running Timers :::::<end>\n\n".$list;
		  	$msg = Text::make_link("Timers currently running", $list);
		}
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
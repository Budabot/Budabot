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
if (preg_match("/^timers? ([0-9]+)$/i", $message, $arr) || preg_match("/^timers? ([0-9]+) (.+)$/i", $message, $arr)) {
	if ($arr[2] == '') {
		$timer_name = 'PrimTimer';
	} else {
		$timer_name = trim($arr[2]);
	}
	
	forEach ($this->vars["Timers"] as $key => $value) {
		if ($this->vars["Timers"][$key]["name"] == $timer_name) {
			$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";

			// Send info back
			bot::send($msg, $sendto);
			return;
		}
	}

	if ($arr[1] < 1) {
		$msg = "No valid time specified!";
		
	    // Send info back
        bot::send($msg, $sendto);
	    return;
	}

	$run_time = $arr[1] * 60;
    $timer = time() + $run_time;

	$this->vars["Timers"][] = array("name" => $timer_name, "owner" => $sender, "mode" => $type, "timer" => $timer, "settime" => time());
    $db->query("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('".str_replace("'", "''", $timer_name)."', '$sender', '$type', $timer, ".time().")");

	$timerset = unixtime_to_readable($run_time);
	$msg = "Timer has been set for $timerset.";
		
    bot::send($msg, $sendto);
} else if (preg_match("/^timers? (([0-9]*)[d|day|days]*).(([0-9]*)[h|hr|hrs]*).(([0-9]*)[m|min|mins]*).(([0-9]*)[s|sec|secs]*)$/i", $message, $arr) || preg_match("/^timers? (([0-9]*)[d|day|days]*).(([0-9]*)[h|hr|hrs]*).(([0-9]*)[m|min|mins]*) (.+)$/i", $message, $arr2)) {
	if ($arr2) {
		$arr = $arr2;
		$last_item = count($arr);
		$timer_name = trim($arr[$last_item - 1]);
	} else {
		$timer_name = 'PrimTimer';
	}
	
	forEach ($this->vars["Timers"] as $key => $value) {
		if ($this->vars["Timers"][$key]["name"] == $timer_name) {
			$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";

			// Send info back
			bot::send($msg, $sendto);
			return;
		}
	}
	
	if (preg_match("/([0-9]+)(d|day|days)/i", $message, $day)) {
		if ($day[1] < 1) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}
		$days = $day[1] * 86400;
	} else {
		$days = 0;
	}
	
	if (preg_match("/([0-9]+)(h|hr|hrs)/i", $message, $hours)) {
		if ($hours[1] < 1) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}
		$hours = $hours[1] * 3600;
	} else {
		$hours = 0;
	}

	if (preg_match("/([0-9]+)(m|min|mins)/i", $message, $mins)) {
		if ($mins[1] < 1) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}
		$mins = $mins[1] * 60;
	} else {
		$mins = 0;
	}
	
	if (preg_match("/([0-9]+)(s|sec|secs)/i", $message, $secs)) {
		if ($secs[1] < 1) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}
		$secs = $secs[1];
	} else {
		$secs = 0;
	}

	if ($days == 0 && $hours == 0 && $mins == 0 && $secs == 0) {
	  	$msg = "No valid Time specified! Please check the helpfiles how to use this command!";
	    // Send info back
	    bot::send($msg, $sendto);
	    return;		  	
	}

	$run_time = $days + $hours + $mins + $secs;
    $timer = time() + $run_time;

	$this->vars["Timers"][] = array("name" => $timer_name, "owner" => $sender, "mode" => $type, "timer" => $timer, "settime" => time());
	$db->query("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('".str_replace("'", "''", $timer_name) ."', '$sender', '$type', $timer, ".time().")");

	$timerset = unixtime_to_readable($run_time);
	$msg = "Timer has been set for $timerset.";
		
    bot::send($msg, $sendto);
} else if (preg_match("/^timers? (rem|del) (.+)$/i", $message, $arr)) {
	$timer_name = strtolower($arr[2]);
	
	forEach ($this->vars["Timers"] as $key => $value) {
		$name = $this->vars["Timers"][$key]["name"];
		$owner = $this->vars["Timers"][$key]["owner"];

		if (strtolower($name) == $timer_name) {
			if ($owner == $sender) {
				unset($this->vars["Timers"][$key]);
				$db->query("DELETE FROM timers_<myname> WHERE `name` = '".str_replace("'", "''", $name)."' AND `owner` = '$sender'");
					
			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;
			} else if (($this->guildmembers[$sender] <= $this->settings['guild admin level']) || isset($this->admins[$sender])) {
				unset($this->vars["Timers"][$key]);
				$db->query("DELETE FROM timers_<myname> WHERE `name` = '".str_replace("'", "''", $name)."'");

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

    bot::send($msg, $sendto);
} else if (preg_match("/^timers?$/i", $message, $arr)) {
	$num_timers = count($this->vars["Timers"]);
	if ($num_timers == 0) {
		$msg = "No Timers running atm.";
	    // Send info back
	    bot::send($msg, $sendto);
	    return;
	}

  	if ($this->settings["timers_window"] == 2 || ($this->settings["timers_window"] >= 3 && $num_timers <= $this->settings["timers_window"])) {
		forEach ($this->vars["Timers"] as $key => $value) {
			$timer = "";
			$tleft = $this->vars["Timers"][$key]["timer"] - time();
			$name = $this->vars["Timers"][$key]["name"];
			$owner = $this->vars["Timers"][$key]["owner"];
			$mode = $this->vars["Timers"][$key]["mode"];

			if ($mode == "msg" && $type == "msg" && ($sender == $owner)) {
				$days = floor($tleft/86400);
				if ($days != 0) {
					$timer .= $days."day(s) ";
				}

				$hours = floor(($tleft-($days*86400))/3600);
				if ($hours != 0) {
					$timer .= $hours."hr(s) ";
				}

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if ($mins != 0) {
					$timer .= $mins."min(s) ";
				}

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if ($secs != 0) {
					$timer .= $secs."sec(s)";
				}
				
				if ($name == "PrimTimer") {
					$msg .= "\n Timer has <highlight>$timer<end> left [set by <highlight>$owner<end>]";
				} else {
					$msg .= "\n Timer <highlight>$name<end> has <highlight>$timer<end> left [set by <highlight>$owner<end>]";  	
				}
			} else if ($mode == $type || ($type == "msg" && $mode != "msg")) {
				$days = floor($tleft/86400);
				if ($days != 0) {
					$timer .= $days."day(s) ";
				}

				$hours = floor(($tleft-($days*86400))/3600);
				if ($hours != 0) {
					$timer .= $hours."hr(s) ";
				}

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if ($mins != 0) {
					$timer .= $mins."min(s) ";
				}

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if ($secs != 0) {
					$timer .= $secs."sec(s)";
				}
				
				if ($name == "PrimTimer") {
					$msg .= "\n Timer has <highlight>$timer<end> left [set by <highlight>$owner<end>]";
				} else {
					$msg .= "\n Timer <highlight>$name<end> has <highlight>$timer<end> left [set by <highlight>$owner<end>]";  	
				}
			}
		}
		if ($msg == "") {
			$msg = "No Timers running atm.";
		} else {
		  	$msg = "Timers currently running:".$msg;
		}
	} else {
		foreach($this->vars["Timers"] as $key => $value) {
			$timer = "";
			$tleft = $this->vars["Timers"][$key]["timer"] - time();
			$name = $this->vars["Timers"][$key]["name"];
			$owner = $this->vars["Timers"][$key]["owner"];
			$mode = $this->vars["Timers"][$key]["mode"];

			if ($mode == "msg" && $type == "msg" && ($sender == $owner)) {
				$days = floor($tleft/86400);
				if ($days != 0) {
					$timer .= $days."day(s) ";
				}

				$hours = floor(($tleft-($days*86400))/3600);
				if ($hours != 0) {
					$timer .= $hours."hr(s) ";
				}

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if ($mins != 0) {
					$timer .= $mins."min(s) ";
				}

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if ($secs != 0) {
					$timer .= $secs."sec(s)";
				}
				
				$list .= "Timername: <highlight>$name<end>\n";
				$list .= "Timeleft: <highlight>$timer<end>\n";
				$list .= "Set by: <highlight>$owner<end>\n\n";
			} else if ($mode == $type  || ($type == "msg" && $mode != "msg")) {
				$days = floor($tleft/86400);
				if ($days != 0) {
					$timer .= $days."day(s) ";
				}

				$hours = floor(($tleft-($days*86400))/3600);
				if ($hours != 0) {
					$timer .= $hours."hr(s) ";
				}

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if ($mins != 0) {
					$timer .= $mins."min(s) ";
				}

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if ($secs != 0) {
					$timer .= $secs."sec(s)";
				}
				
				$list .= "Timername: <highlight>$name<end>\n";
				$list .= "Timeleft: <highlight>$timer<end>\n";
				$list .= "Set by: <highlight>$owner<end>\n\n";
			}
		}
		if ($list == "") {
			$msg = "No Timers running atm.";
		} else {
			$list = "<header>::::: Currently running Timers :::::<end>\n\n".$list;
		  	$msg = bot::makeLink("Timers currently running", $list);
		}
	}

    bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
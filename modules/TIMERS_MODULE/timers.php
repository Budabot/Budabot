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
if (preg_match("/^timers? ([0-9]+)$/i", $message, $arr) ) {
  	if($arr[1] < 1 || $arr[1] > 10000) {
		$msg = "No valid time specified!";
		
	    // Send info back
        bot::send($msg, $sendto);
	    return;
	}

	$arr[1] *= 60;
    $timer = time() + $arr[1];

	$found = false;
	foreach($this->vars["Timers"] as $key => $value) {
	  	if($this->vars["Timers"][$key]["owner"] == $sender && $this->vars["Timers"][$key]["name"] == "PrimTimer") {
		    $found = true;
		    break;
		}
	}
			
	if($found) {
	  	$msg = "<highlight>$sender<end> you have already a primary Timer running.";

  	    // Send info back
	    bot::send($msg, $sendto);
		return;
	}

	$this->vars["Timers"][] = array("name" => "PrimTimer", "owner" => $sender, "mode" => $type, "timer" => $timer, "settime" => time());
    $db->query("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('PrimTimer', '$sender', '$type', $timer, '".time()."')");
	$days = floor($arr[1]/86400);
	if($days != 0)
		$timerset .= $days."day(s) ";
	$hours = floor(($arr[1]-($days*86400))/3600);
	if($hours != 0)
		$timerset .= $hours."hr(s) ";
	$mins = ceil(($arr[1]-($days*86400)-$hours*3600)/60);
	if($mins != 0)
		$timerset .= $mins."min(s)";
	$msg = "Timer has been set for $timerset.";
		
    // Send info back
    bot::send($msg, $sendto);
} elseif (preg_match("/^timers? ([0-9]+) (.+)$/i", $message, $arr)) {
  	$timer_name = trim($arr[2]);
	
  	if($arr[1] < 1 || $arr[1] > 10000) {
		$msg = "No valid time specified!";
		
	    // Send info back
	    bot::send($msg, $sendto);
	    return;
	}

	$arr[1] *= 60;
    $timer = time() + $arr[1];

	$found = false;
	foreach($this->vars["Timers"] as $key => $value) {
	  	if($this->vars["Timers"][$key]["name"] == $timer_name) {
		    $found = true;
		    break;
		}
	}
			
	if($found) {
	  	$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";

  	    // Send info back
	    bot::send($msg, $sendto);
		return;
	}
	
	$days = floor($arr[1]/86400);
	if($days != 0)
		$timerset .= $days."day(s) ";

	$hours = floor(($arr[1]-($days*86400))/3600);
	if($hours != 0)
		$timerset .= $hours."hr(s) ";

	$mins = ceil(($arr[1]-($days*86400)-$hours*3600)/60);
	if($mins != 0)
		$timerset .= $mins."min(s)";

  	$this->vars["Timers"][] = array("name" => $timer_name, "owner" => $sender, "mode" => $type, "timer" => $timer, "settime" => time());

    $db->query("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('".str_replace("'", "''", $timer_name)."', '$sender', '$type', $timer, ".time().")");	

	$msg = "Timer <highlight>$timer_name<end> has been set for $timerset.";
		
    // Send info back
    bot::send($msg, $sendto);
} elseif (preg_match("/^timers? (rem|del) (.+)$/i", $message, $arr)) {
	$timer_name = strtolower($arr[2]);
	
	foreach($this->vars["Timers"] as $key => $value) {
		$name = $this->vars["Timers"][$key]["name"];
		$owner = $this->vars["Timers"][$key]["owner"];

		if(strtolower($name) == $timer_name) {
			if($owner == $sender) {
				unset($this->vars["Timers"][$key]);
				$db->query("DELETE FROM timers_<myname> WHERE `name` = '".str_replace("'", "''", $name)."' AND `owner` = '$sender'");
					
			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;
			} elseif(($this->guildmembers[$sender] <= $this->settings['guild admin level']) || isset($this->admins[$sender])) {
				unset($this->vars["Timers"][$key]);
				$db->query("DELETE FROM timers_<myname> WHERE `name` = '".str_replace("'", "''", $name)."'");

			  	$msg = "Removed timer <highlight>$name<end>.";
			  	break;			  	
			} else {
				$msg = "You don't have the right to remove this timer.";
			}
		}
	}

	if(!$msg) {
		$msg = "A timer with this name is not running.";
	}

    // Send info back
    bot::send($msg, $sendto);
} elseif (preg_match("/^timers? (([0-9]*)[d|day|days]*).(([0-9]*)[h|hr|hrs]*).(([0-9]*)[m|min|mins]*)$/i", $message, $arr)) {
	if(preg_match("/([0-9]+)(d|day|days)/i", $message, $day)) {
		if($day[1] < 1 || $day[1] > 10) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}
		$days = $day[1] * 86400;
	} else
		$days = 0;
	
	if(preg_match("/([0-9]+)(h|hr|hrs)/i", $message, $hours)) {
		if($hours[1] < 1 || $hours[1] > 50) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}
		$hours = $hours[1] * 3600;
	} else
		$hours = 0;	

	if(preg_match("/([0-9]+)(m|min|mins)/i", $message, $mins)) {
		if($mins[1] < 1 || $mins[1] > 300) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}
		$mins = $mins[1] * 60;
	} else
		$mins = 0;	

	if($days == 0 && $hours == 0 && $mins == 0) {
	  	$msg = "No valid Time specified! Please check the helpfiles how to use this command!";
	    // Send info back
	    bot::send($msg, $sendto);
	    return;		  	
	}

    $timer = time() + $days + $hours + $mins;
	$run_time = $days + $hours + $mins;

	$found = false;
	foreach($this->vars["Timers"] as $key => $value) {
	  	if($this->vars["Timers"][$key]["owner"] == $sender && $this->vars["Timers"][$key]["name"] == "PrimTimer") {
		    $found = true;
		    break;
		}
	}
			
	if($found) {
	  	$msg = "<highlight>$sender<end> you have already a primary Timer running.";

  	    // Send info back
	    bot::send($msg, $sendto);
		return;
	}

	$this->vars["Timers"][] = array("name" => "PrimTimer", "owner" => $sender, "mode" => $type, "timer" => $timer, "settime" => time());
    $db->query("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('PrimTimer', '$sender', '$type', $timer, '".time()."')");
	$days = floor($run_time/86400);
	if($days != 0)
		$timerset .= $days."day(s) ";
	$hours = floor(($run_time-($days*86400))/3600);
	if($hours != 0)
		$timerset .= $hours."hr(s) ";
	$mins = ceil(($run_time-($days*86400)-$hours*3600)/60);
	if($mins != 0)
		$timerset .= $mins."min(s)";
	$msg = "Timer has been set for $timerset.";
		
    // Send info back
    bot::send($msg, $sendto);
} elseif (preg_match("/^timers? (([0-9]*)[d|day|days]*).(([0-9]*)[h|hr|hrs]*).(([0-9]*)[m|min|mins]*) (.+)$/i", $message, $arr)) {
	$last_item = count($arr);
	$timer_name = trim($arr[$last_item - 1]);
	
	if(preg_match("/([0-9]+)(d|day|days)/i", $message, $day)) {
		if($day[1] < 1 || $day[1] > 14) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}

		$days = $day[1];
	} else
		$days = 0;
	
	if(preg_match("/([0-9]+)(h|hr|hrs)/i", $message, $hours)) {
		if($hours[1] < 1 || $hours[1] > 50) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}

		$hours = $hours[1];
	} else
		$hours = 0;	

	if(preg_match("/([0-9]+)(m|min|mins)/i", $message, $mins)) {
		if($mins[1] < 1 || $mins[1] > 300) {
			$msg = "No valid time specified!";
			
		    // Send info back
		    bot::send($msg, $sendto);
		    return;		  	
		}

		$mins = $mins[1];
	} else
		$mins = 0;	

	if($days == 0 && $hours == 0 && $mins == 0) {
	  	$msg = "No valid Time specified! Please check the helpfiles how to use this command!";
	    // Send info back
	    bot::send($msg, $sendto);
	    return;		  	
	}

    $timer = time() + ($days*86400) + ($hours*3600) + ($mins*60);

	$found = false;
	foreach($this->vars["Timers"] as $key => $value) {
	  	if($this->vars["Timers"][$key]["name"] == $timer_name) {
		    $found = true;
		    break;
		}
	}
			
	if($found) {
	  	$msg = "A Timer with the name <highlight>$timer_name<end> is already running.";

  	    // Send info back
	    bot::send($msg, $sendto);
		return;
	}

	$timerset = "";
	if($days != 0)
		$timerset .= $days."day(s) ";
	if($hours != 0)
		$timerset .= $hours."hr(s) ";
	if($mins != 0)
		$timerset .= $mins."min(s)";

  	$this->vars["Timers"][] = array("name" => $timer_name, "owner" => $sender, "mode" => $type, "timer" => $timer, "settime" => time());
	$db->query("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('".str_replace("'", "''", $timer_name) ."', '$sender', '$type', $timer, ".time().")");

    
	$msg = "Timer <highlight>$timer_name<end> has been set for $timerset.";
		
    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
   	    bot::send($msg);
    elseif($type == "guild")
      	bot::send($msg, "guild");      	      	      	
} elseif (preg_match("/^timers?$/i", $message, $arr)) {
	$num_timers = count($this->vars["Timers"]);
	if($num_timers == 0) {
		$msg = "No Timers running atm.";
	    // Send info back
	    bot::send($msg, $sendto);
	    return;
	}

  	if($this->settings["timers_window"] == 2 || ($this->settings["timers_window"] >= 3 && $num_timers <= $this->settings["timers_window"])) {
		foreach($this->vars["Timers"] as $key => $value) {
			$timer = "";
			$tleft = $this->vars["Timers"][$key]["timer"] - time();
			$name = $this->vars["Timers"][$key]["name"];
			$owner = $this->vars["Timers"][$key]["owner"];
			$mode = $this->vars["Timers"][$key]["mode"];

			if($mode == "msg" && $type == "msg" && ($sender == $owner)) {
				$days = floor($tleft/86400);
				if($days != 0)
					$timer .= $days."day(s) ";

				$hours = floor(($tleft-($days*86400))/3600);
				if($hours != 0)
					$timer .= $hours."hr(s) ";

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if($mins != 0)
					$timer .= $mins."min(s) ";

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if($secs != 0)
					$timer .= $secs."sec(s)";
				
				if($name == "PrimTimer")
					$msg .= "\n Timer has <highlight>$timer<end> left [set by <highlight>$owner<end>]";
				else
					$msg .= "\n Timer <highlight>$name<end> has <highlight>$timer<end> left [set by <highlight>$owner<end>]";  	
			} elseif($mode == $type || ($type == "msg" && $mode != "msg")) {
				$days = floor($tleft/86400);
				if($days != 0)
					$timer .= $days."day(s) ";

				$hours = floor(($tleft-($days*86400))/3600);
				if($hours != 0)
					$timer .= $hours."hr(s) ";

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if($mins != 0)
					$timer .= $mins."min(s) ";

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if($secs != 0)
					$timer .= $secs."sec(s)";
				
				if($name == "PrimTimer")
					$msg .= "\n Timer has <highlight>$timer<end> left [set by <highlight>$owner<end>]";
				else
					$msg .= "\n Timer <highlight>$name<end> has <highlight>$timer<end> left [set by <highlight>$owner<end>]";  	
			}
		}
		if($msg == "")
			$msg = "No Timers running atm.";
		else
		  	$msg = "Timers currently running:".$msg;
	} else {
		foreach($this->vars["Timers"] as $key => $value) {
			$timer = "";
			$tleft = $this->vars["Timers"][$key]["timer"] - time();
			$name = $this->vars["Timers"][$key]["name"];
			$owner = $this->vars["Timers"][$key]["owner"];
			$mode = $this->vars["Timers"][$key]["mode"];

			if($mode == "msg" && $type == "msg" && ($sender == $owner)) {
				$days = floor($tleft/86400);
				if($days != 0)
					$timer .= $days."day(s) ";

				$hours = floor(($tleft-($days*86400))/3600);
				if($hours != 0)
					$timer .= $hours."hr(s) ";

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if($mins != 0)
					$timer .= $mins."min(s) ";

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if($secs != 0)
					$timer .= $secs."sec(s)";
				
				$list .= "Timername: <highlight>$name<end>\n";
				$list .= "Timeleft: <highlight>$timer<end>\n";
				$list .= "Set by: <highlight>$owner<end>\n\n";
			} elseif($mode == $type  || ($type == "msg" && $mode != "msg")) {
				$days = floor($tleft/86400);
				if($days != 0)
					$timer .= $days."day(s) ";

				$hours = floor(($tleft-($days*86400))/3600);
				if($hours != 0)
					$timer .= $hours."hr(s) ";

				$mins = floor(($tleft-($days*86400)-$hours*3600)/60);
				if($mins != 0)
					$timer .= $mins."min(s) ";

				$secs = $tleft-($days*86400)-($hours*3600)-$mins*60;
				if($secs != 0)
					$timer .= $secs."sec(s)";
				
				$list .= "Timername: <highlight>$name<end>\n";
				$list .= "Timeleft: <highlight>$timer<end>\n";
				$list .= "Set by: <highlight>$owner<end>\n\n";
			}
		}
		if($list == "")
			$msg = "No Timers running atm.";
		else {
			$list = "<header>::::: Currently running Timers :::::<end>\n\n".$list;
		  	$msg = bot::makeLink("Timers currently running", $list);
		}
	}

    // Send info back
    bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
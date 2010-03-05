<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows APF opening timer
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 22.03.2006
   ** Date(last modified): 24.03.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

if(eregi("^pf$", $message)) {
  	if($this->settings["server_status"] == "down") {
  		$msg = "AO Server is down.";
  		    // Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");
	    return;	    
	}

  	if($this->settings["pftimer_status"] == "no_correction" && (!isset($this->admins[$sender]) || $type != "msg")) {
  		$msg = "The timer didn´t got corrected after the downtime yet.";
  		    // Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");
	    return;	    
	} elseif($this->settings["pftimer_status"] == "no_correction" && isset($this->admins[$sender])) {
		$time = $this->settings["pftimer"];
		$timeraid = 7*60*60+12*60;
		
		$raids = floor((time() - $time) / $timeraid);
		$timeleft = ($time + $timeraid + $timeraid * $raids) - mktime();
	
		$mins = floor($timeleft / 60);
		$hours = floor($mins / 60);
		$mins = floor($mins - ($hours * 60));
		$seconds = $timeleft - ($hours*60*60) - ($mins*60);
	
		if($seconds < 10)
			$seconds = "0".$seconds;
	
		if($mins < 10)
			$mins = "0".$mins;
		
		if($hours == 0)
			$hours = "00";
		else
			$hours = "0".$hours;
	
		if($timeleft >= 7*60*60)
			$msg = "Sector gates are opened, you have <highlight>$mins:$seconds<end> until they are closing!";
		else
			$msg = "<highlight>$hours:$mins:$seconds<end> remaining until gates are opening.";
	
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");
	       	
  		$msg = "<red>Only admins can see the timer atm. After its correction unlock it!<end>";
  		    // Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");
	       	
	    return;
	}
  	
	$time = $this->settings["pftimer"];
	$timeraid = 7*60*60+12*60;
	
	$raids = floor((time() - $time) / $timeraid);
	$timeleft = ($time + $timeraid + $timeraid * $raids) - mktime();

	$mins = floor($timeleft / 60);
	$hours = floor($mins / 60);
	$mins = floor($mins - ($hours * 60));
	$seconds = $timeleft - ($hours*60*60) - ($mins*60);

	if($seconds < 10)
		$seconds = "0".$seconds;

	if($mins < 10)
		$mins = "0".$mins;
	
	if($hours == 0)
		$hours = "00";
	else
		$hours = "0".$hours;

	if($timeleft >= 7*60*60)
		$msg = "Sector gates are opened, you have <highlight>$mins:$seconds<end> until they are closing!";
	else
		$msg = "<highlight>$hours:$mins:$seconds<end> remaining until gates are opening.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
} else
	$syntax_error = true;
?>
<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: Adds a Player to the banlist
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 21.11.2006
   **
   ** Copyright (C) 2005, 2006 J Gracik
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

if (preg_match("/^ban ([0-9]+)(w|week|weeks|m|month|months|d|day|days) (.+) (for|reason) (.+)$/i", $message, $arr)) {
  	$why = $arr[5];
	if (($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] <= 50 && $arr[1] > 0) {
	    $ban_end = time() + ($arr[1] * 604800);
	} else if (($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] > 50) {
	  	bot::send("You can't ban a player for more then 50weeks!", $sendto);
	  	return;
	} else if (($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] <= 100 && $arr[1] > 0) {
	    $ban_end = time() + ($arr[1] * 86400);
	} else if (($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] > 100) {
	  	bot::send("You can't ban a player for more then 100days!", $sendto);
	  	return;
	} else if (($arr[2] == "m" || $arr[2] == "month" || $arr[2] == "months") && $arr[1] <= 12 && $arr[1] > 0) {
	    $ban_end = time() + ($arr[1] * 18144000);
	} else {
	  	bot::send("You can't ban a player for more then 12months!", $sendto);
	  	return;
	}
	
	$who = ucfirst(strtolower($arr[3]));
	
	if (AOChat::get_uid($who) == NULL) {
		bot::send("<red>Sorry the player you wish to ban does not exist.", $sendto);
		return;
	}
	
	if ($this->banlist[$who]["name"] == $who) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
	
	$why = str_replace(";", "", $arr[5]);
	
	$this->banlist[$who]["name"] = $who;
	$this->banlist[$who]["admin"] = $sender;
	$this->banlist[$who]["when"] = date("m-d-y");
	$this->banlist[$who]["banend"] = $ban_end;
	$this->banlist["$who"]["reason"] = $why;

	$db->query("INSERT INTO banlist_<myname> (`name`, `admin`, `time`, `why`, `banend`) VALUES ('$who', '$sender', '".date("m-d-y")."', '$why', $ban_end)");
	if ($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") {
		$value = "week(s)";
	} else {
		$value = "month(s)";
	}

	bot::send("You have banned <highlight>$who<end> for {$arr[1]}$value from this bot.", $sendto);
	bot::send("You have been banned from this bot by $sender for {$arr[1]}$value.\n Reason: $why", $who);
} else if (preg_match("/^ban ([0-9]+)(w|week|weeks|m|month|months|d|day|days) (.+)$/i", $message, $arr)) {
	if (($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] <= 50 && $arr[1] > 0) {
	    $ban_end = time() + ($arr[1] * 604800);
	} else if (($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] > 50) {
	  	bot::send("You can't ban a player for more then 50weeks!", $sendto);
	  	return;
	} else if (($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] <= 100 && $arr[1] > 0) {
	    $ban_end = time() + ($arr[1] * 86400);
	} else if (($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] > 100) {
	  	bot::send("You can't ban a player for more then 100days!", $sendto);
	  	return;
	} else if (($arr[2] == "m" || $arr[2] == "month" || $arr[2] == "months") && $arr[1] <= 12 && $arr[1] > 0) {
	    $ban_end = time() + ($arr[1] * 18144000);
	} else {
	  	bot::send("You can't ban a player for more then 12months!", $sendto);
	  	return;
	}
	
	$who = ucfirst(strtolower($arr[3]));
	
	if (AOChat::get_uid($who) == NULL) {
		bot::send("<red>Sorry the player you wish to ban does not exist.", $sendto);
		return;
	}
	
	if ($this->banlist[$who]["name"] == $who) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
	
	$this->banlist[$who]["name"] = $who;
	$this->banlist[$who]["admin"] = $sender;
	$this->banlist[$who]["when"] = date("m-d-y");
	$this->banlist[$who]["banend"] = $ban_end;

	$db->query("INSERT INTO banlist_<myname> (`name`, `admin`, `time`, `banend`) VALUES ('$who', '$sender', '".date("m-d-y")."', $ban_end)");
	if ($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") {
		$value = "week(s)";
	} else {
		$value = "month(s)";
	}

	bot::send("You have banned <highlight>$who<end> for {$arr[1]}$value from this bot.", $sendto);
} else if (preg_match("/^ban (.+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if (AOChat::get_uid($who) == NULL){
		bot::send("<red>Sorry player you wish to ban does not exist.", $sendto);
		return;
	}

	if ($this->banlist[$who]["name"] == $who) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
		
	$why = str_replace(";", "", $arr[3]);
	
	$this->banlist["$who"]["name"] = $who;
	$this->banlist["$who"]["admin"] = $sender;
	$this->banlist["$who"]["when"] = date("m-d-y");
	$this->banlist["$who"]["reason"] = $why;
	$db->query("INSERT INTO banlist_<myname> (`name`, `admin`, `time`, `why`) VALUES ('$who', '$sender', '".date("m-d-y")."', '$why')");

	bot::send("You have banned <highlight>$who<end> from this bot", $sendto);
	bot::send("You have been banned from this bot by $sender.\n Reason: $why", $who);
} else if (preg_match("/^ban (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if (AOChat::get_uid($who) == NULL) {
		bot::send("<red>Sorry player you wish to ban does not exist.", $sendto);
		return;
	}

	if ($this->banlist[$who]["name"] == $who) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
	
	$this->banlist["$who"]["name"] = $who;
	$this->banlist["$who"]["admin"] = $sender;
	$this->banlist["$who"]["when"] = date("m-d-y");

	$db->query("INSERT INTO banlist_<myname> (`name`, `admin`, `time`) VALUES ('$who', '$sender', '".date("m-d-y")."')");
	bot::send("You have banned <highlight>$who<end> from this bot", $sendto);
} else if (preg_match("/^banorg (.+)$/i", $message, $arr)) {
	$who = $arr[1];
	
	if ($this->banlist[$who]["name"] == $who) {
	  	bot::send("<red>The organization $who is already banned.<end>", $sendto);
		return;
	}
	
	$this->banlist["$who"]["name"] = $who;
	$this->banlist["$who"]["admin"] = $sender;
	$this->banlist["$who"]["when"] = date("m-d-y");

	$db->query("INSERT INTO banlist_<myname> (`name`, `admin`, `time`) VALUES ('$who', '$sender', '".date("m-d-y")."')");
	bot::send("You have banned ALL members of <highlight>$who<end> from this bot", $sendto);
} else {
	$syntax_error = true;
}

?>
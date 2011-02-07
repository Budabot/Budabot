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

if (preg_match("/^ban (.+) ([0-9]+)(w|week|weeks|m|month|months|d|day|days) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[5];

	if (AOChat::get_uid($who) == NULL) {
		bot::send("<red>Sorry the player you wish to ban does not exist.", $sendto);
		return;
	}
	
	if (Ban::is_banned($who)) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
	
	if (($arr[3] == "w" || $arr[3] == "week" || $arr[3] == "weeks") && $arr[2] > 0) {
	    $length = ($arr[2] * 604800);
	} else if (($arr[3] == "d" || $arr[3] == "day" || $arr[3] == "days") && $arr[2] > 0) {
	    $length = ($arr[2] * 86400);
	} else if (($arr[3] == "m" || $arr[3] == "month" || $arr[3] == "months") && $arr[2] > 0) {
	    $length = ($arr[2] * 18144000);
	}

	Ban::add($who, $sender, $length, $reason);

	bot::send("You have banned <highlight>$who<end> from this bot.", $sendto);
	bot::send("You have been banned from this bot by $sender. Reason: $reason", $who);
} else if (preg_match("/^ban (.+) ([0-9]+)(w|week|weeks|m|month|months|d|day|days)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if (AOChat::get_uid($who) == NULL) {
		bot::send("<red>Sorry the player you wish to ban does not exist.", $sendto);
		return;
	}
	
	if (Ban::is_banned($who)) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
	
	if (($arr[3] == "w" || $arr[3] == "week" || $arr[3] == "weeks") && $arr[2] > 0) {
	    $length = ($arr[2] * 604800);
	} else if (($arr[3] == "d" || $arr[3] == "day" || $arr[3] == "days") && $arr[2] > 0) {
	    $length = ($arr[2] * 86400);
	} else if (($arr[3] == "m" || $arr[3] == "month" || $arr[3] == "months") && $arr[2] > 0) {
	    $length = ($arr[2] * 18144000);
	}
	
	Ban::add($who, $sender, $length, '');

	bot::send("You have banned <highlight>$who<end> from this bot.", $sendto);
	bot::send("You have been banned from this bot by $sender.", $who);
} else if (preg_match("/^ban (.+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[3];
	
	if (AOChat::get_uid($who) == NULL) {
		bot::send("<red>Sorry player you wish to ban does not exist.", $sendto);
		return;
	}

	if (Ban::is_banned($who)) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
		
	Ban::add($who, $sender, null, $reason);

	bot::send("You have banned <highlight>$who<end> from this bot.", $sendto);
	bot::send("You have been banned from this bot by $sender. Reason: $reason", $who);
} else if (preg_match("/^ban (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if (AOChat::get_uid($who) == NULL) {
		bot::send("<red>Sorry player you wish to ban does not exist.", $sendto);
		return;
	}

	if (Ban::is_banned($who)) {
	  	bot::send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
	
	Ban::add($who, $sender, null, '');

	bot::send("You have banned <highlight>$who<end> from this bot.", $sendto);
	bot::send("You have been banned from this bot by $sender.", $who);
} else if (preg_match("/^banorg (.+)$/i", $message, $arr)) {
	$who = $arr[1];
	
	if (Ban::is_banned($who)) {
	  	bot::send("<red>The organization $who is already banned.<end>", $sendto);
		return;
	}
	
	Ban::add($who, $sender, null, '');

	bot::send("You have banned the org <highlight>$who<end> from this bot.", $sendto);
} else {
	$syntax_error = true;
}

?>
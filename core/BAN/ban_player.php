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

if(preg_match("/^ban ([0-9]+)(w|week|weeks|m|month|months|d|day|days) (.+) (for|reason) (.+)$/i", $message, $arr)) {
  	$reason = $arr[5];
	$name = ucfirst(strtolower($arr[3]));

	if(($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] <= 50 && $arr[1] > 0)
	    $ban_end = time() + ($arr[1] * 604800);
	elseif (($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] > 50) {
	  	$this->send("You can't ban a player for more then 50weeks!", $sendto);
	  	return;
	} elseif(($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] <= 100 && $arr[1] > 0)
	    $ban_end = time() + ($arr[1] * 86400);
	elseif (($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] > 100) {
	  	$this->send("You can't ban a player for more then 100days!", $sendto);
	  	return;
	} elseif(($arr[2] == "m" || $arr[2] == "month" || $arr[2] == "months") && $arr[1] <= 12 && $arr[1] > 0)
	    $ban_end = time() + ($arr[1] * 18144000);
	else {
	  	$this->send("You can't ban a player for more then 12months!", $sendto);
	  	return;
	}
} elseif(preg_match("/^ban ([0-9]+)(w|week|weeks|m|month|months|d|day|days) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[3]));
	$reason = '';

	if(($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] <= 50 && $arr[1] > 0)
	    $ban_end = time() + ($arr[1] * 604800);
	elseif (($arr[2] == "w" || $arr[2] == "week" || $arr[2] == "weeks") && $arr[1] > 50) {
	  	$this->send("You can't ban a player for more then 50weeks!", $sendto);
	  	return;
	} elseif(($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] <= 100 && $arr[1] > 0)
	    $ban_end = time() + ($arr[1] * 86400);
	elseif (($arr[2] == "d" || $arr[2] == "day" || $arr[2] == "days") && $arr[1] > 100) {
	  	$this->send("You can't ban a player for more then 100days!", $sendto);
	  	return;
	} elseif(($arr[2] == "m" || $arr[2] == "month" || $arr[2] == "months") && $arr[1] <= 12 && $arr[1] > 0)
	    $ban_end = time() + ($arr[1] * 18144000);
	else {
	  	$this->send("You can't ban a player for more then 12months!", $sendto);
	  	return;
	}
} elseif(preg_match("/^ban (.+) (for|reason) (.+)$/i", $message, $arr)){
	$name = ucfirst(strtolower($arr[1]));
	$reason = str_replace(";", "", $arr[3]);
	$banend = "NULL";
} elseif(preg_match("/^ban (.+)$/i", $message, $arr)){
	$name = ucfirst(strtolower($arr[1]));
	$reason = '';
	$banend = "NULL";
} else {
	$syntax_error = true;
	return;
}

if($this->get_uid($who) == NULL){
	$this->send("<red>Sorry player you wish to ban does not exist.", $sendto);
	return;
}

if (isset($this->banlist[$who])) {
	$this->send("<red>Player $who is already banned.<end>", $sendto);
	return;
}

$db->exec("INSERT INTO banlist_<myname> (`name`, `banned_by`, `time`, `reason`, `banend`) VALUES ('".str_replace("'", "''", $name)."', '$sender', '".date("m-d-y")."', '".str_replace("'", "''", $reason)."', $banend)");
include 'upload_banlist.php';
$this->send("You have banned <highlight>$name<end> from this bot", $sendto);

?>
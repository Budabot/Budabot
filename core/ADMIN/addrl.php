<?php
   /*
   ** Author: Sebuda, Derroylo (RK2)
   ** Description: Adds a RL to the adminlist
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 30.01.2007
   **
   ** Copyright (C) 2005, 2006, 2007 J. Gracik, C. Lohmann
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

if (preg_match("/^addrl (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL){
		$chatBot->send("<red>Sorry player you wish to add doesn't exist.<end>", $sendto);
		return;
	}
	
	if ($who == $sender) {
		$chatBot->send("<red>You can't add yourself to another group.<end>", $sendto);
		return;
	}
	
	$ai = Alts::get_alt_info($who);
	if (Setting::get("alts_inherit_admin") == 1 && $ai->main != $who) {
		$msg = "<red>Alts inheriting admin is enabled, and $who is not a main character.<end>";
		if ($chatBot->admins[$ai->main]["level"] == 2) {
			$msg .= " {$ai->main} is already a raidleader.";
		} else {
			$msg .= " Try again with $who's main, <highlight>{$ai->main}<end>.";
		}
		$chatBot->send($msg, $sendto);
		return;
	}

	if ($chatBot->admins[$who]["level"] == 2) {
		$chatBot->send("<red>Sorry but $who is already a raidleader.<end>", $sendto);
		return;
	}
	
	if ((int)$chatBot->admins[$sender]["level"] <= (int)$chatBot->admins[$who]["level"]){
		$chatBot->send("<red>You must have a rank higher then $who.<end>", $sendto);
		return;
	}

	if (isset($chatBot->admins[$who]["level"]) && $chatBot->admins[$who]["level"] > 2) {
		$chatBot->send("<highlight>$who<end> has been demoted to a raidleader.", $sendto);
		$chatBot->send("You have been demoted to raidleader", $who);
		$db->exec("UPDATE admin_<myname> SET `adminlevel` = 2 WHERE `name` = '$who'");
		$chatBot->admins[$who]["level"] = 3;
	} else {
		$db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (2, '$who')");
		$chatBot->admins[$who]["level"] = 2;
		$chatBot->send("<highlight>$who<end> has been added as a raidleader", $sendto);
		$chatBot->send("You got raidleader access to <myname>", $who);
	}

	Buddylist::add($who, 'admin');
} else {
	$syntax_error = true;
}

?>
<?php
   /*
   ** Author: Sebuda, Derroylo (RK2)
   ** Description: Adds a mod to the adminlist
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

if (preg_match("/^addmod (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));

	if (AOChat::get_uid($who) == NULL){
		bot::send("<red>Sorry the player you wish to add doesn't exist.<end>", $sendto);
		return;
	}
	
	if ($who == $sender) {
		bot::send("<red>You can't add yourself to another group.<end>", $sendto);
		return;
	}


	if ($this->admins[$who]["level"] == 3) {
		bot::send("<red>Sorry but $who is already a moderator.<end>", $sendto);
		return;
	}
	
	if ($this->settings["Super Admin"] != $sender && (int)$this->admins[$sender]["level"] <= (int)$this->admins[$who]["level"]){
		bot::send("<red>You must have a rank higher then $who.<end>", $sendto);
		return;
	}

	if (isset($this->admins[$who]["level"]) && $this->admins[$who]["level"] >= 2) {
		if($this->admins[$who]["level"] > 3) {
			bot::send("<highlight>$who<end> has been demoted to the rank of a Moderator.", $sendto);
			bot::send("You have been demoted to the rank of a Moderator on {$this->vars["name"]}", $who);
		} else {
			bot::send("<highlight>$who<end> has been promoted to the rank of a Moderator.", $sendto);
			bot::send("You have been promoted to the rank of a Moderator on {$this->vars["name"]}", $who);
		}
		$db->exec("UPDATE admin_<myname> SET `adminlevel` = 3 WHERE `name` = '$who'");
		$this->admins[$who]["level"] = 3;
	} else {
		$db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (3, '$who')");
		$this->admins[$who]["level"] = 3;
		bot::send("<highlight>$who<end> has been added to the Moderatorgroup", $sendto);
		bot::send("You got moderator access to <myname>", $who);
	}

	Buddylist::add($who, 'admin');
} else {
	$syntax_error = true;
}
?>
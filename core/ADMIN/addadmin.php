<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Adds a Administrator to the adminlist
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 30.01.2007
   ** Date(last modified): 30.01.2007
   **
   ** Copyright (C) 2007 C. Lohmann
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

if (preg_match("/^addadmin (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));

	if (AOChat::get_uid($who) == NULL){
		bot::send("<red>Sorry the player you wish to add doesn't exist.<end>", $sendto);
		return;
	}
	
	if ($who == $sender) {
		bot::send("<red>You can't add yourself to another group.<end>", $sendto);
		return;
	}

	if ($this->admins[$who]["level"] == ADMIN) {
		bot::send("<red>Sorry but $who is already a Administrator.<end>", $sendto);
		return;
	}
	
	if ($this->settings["Super Admin"] != $sender){
		bot::send("<red>You need to be Super-Administrator to add a Administrator<end>", $sendto);
		return;
	}

	if (isset($this->admins[$who]["level"])) {
		bot::send("<highlight>$who<end> has been promoted to the rank of a Administrator.", $sendto);
		bot::send("You have been promoted to the rank of a Administrator on {$this->vars["name"]}", $who);
		$db->query("UPDATE admin_<myname> SET `adminlevel` = ". ADMIN . " WHERE `name` = '$who'");
		$this->admins[$who]["level"] = ADMIN;
	} else {
		$db->query("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (" . ADMIN . ", '$who')");
		$this->admins[$who]["level"] = ADMIN;
		bot::send("<highlight>$who<end> has been added to the Administrator group", $sendto);
		bot::send("You got Administrator access to <myname>", $who);
	}

	$this->add_buddy($who, 'admin');
} else {
	$syntax_error = true;
}
?>
<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Removes a admin from the adminlist
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

if (preg_match("/^kickadmin (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));
	if(AOChat::get_uid($who) == NULL){
		bot::send("<red>Sorry player you wish to remove does not exist.", $sendto);
		return;
	}

	if ($who == $sender) {
		bot::send("<red>You can't kick yourself.<end>", $sendto);
		return;
	}

	if ($this->admins[$who]["level"] != 4) {
		bot::send("<red>Sorry $who is not a Administrator of this Bot.<end>", $sendto);
		return;
	}
	
	if ($this->settings["Super Admin"] != $sender){
		bot::send("<red>You need to be Super-Administrator to kick a Administrator<end>", $sendto);
		return;
	}
	
	unset($this->admins[$who]);
	$db->query("DELETE FROM admin_<myname> WHERE `name` = '$who'");
	
	$this->remove_buddy($who, 'admin');
	
	bot::send("<highlight>$who<end> has been removed as Administrator of this Bot.", $sendto);
	bot::send("Your Administrator access to <myname> has been removed.", $who);
} else {
	$syntax_error = true;
}
?>
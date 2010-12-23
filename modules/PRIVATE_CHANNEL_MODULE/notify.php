<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Private Channel (Notify on Join)
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 18.02.2006
   ** Date(last modified): 21.11.2006
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

if ($type == "joinPriv") {
	$this->vars["Guest"][$sender] = true;
	$whois = Player::get_by_name($sender);
	$db->query("INSERT INTO priv_chatlist_<myname> (`name`) VALUES ('$sender')");

	$msg = "<highlight>{$sender}<end> (<highlight>{$whois->level}<end>/<green>{$whois->ai_level}<end>, <highlight>{$whois->profession}<end>, {$whois->faction}) has joined the private channel";

	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "guild", true);
	}
	bot::send($msg, "priv", true);
} else if ($type == "leavePriv") {
	$db->query("DELETE FROM priv_chatlist_<myname> WHERE `name` = '$sender'");
	unset($this->vars["Guest"][$sender]);

	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "guild", true);
	}
	bot::send($msg, "priv", true);
}

?>
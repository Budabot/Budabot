<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Handles the Privatechat join
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
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

if(!isset($this->vars["Guest"][$sender])) {
	$whois = new whois($sender);
	$db->query("INSERT INTO priv_chatlist_<myname> (`name`, `faction`, `profession`, `guild`, `breed`, `level`, `ai_level`) ".
				"VALUES ('$sender', '$whois->faction', '$whois->prof', '$whois->org', '$whois->breed', '$whois->level', '$whois->ai_level')");

	if(isset($this->admins[$sender]) && $this->admins[$sender]["level"] >= 2 && $this->settings["adminnews"] != "Not set.") {
		bot::send("<red>Admin News:<end> <yellow>".$this->settings["adminnews"]."<end>", $sender);
	} 
	if($this->settings["news"] != "Not set.") {
		bot::send("<red>News:<end> <yellow>".$this->settings["news"]."<end>", $sender);
	}
}
?>

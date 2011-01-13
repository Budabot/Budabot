<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Private Channel (relay to org chat)
   ** Version: 1.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 02.12.2006
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

// Check if the private channel relay is enabled
if ($this->settings["guest_relay"] != 1) {
	return;
}

// Check that it's not a command or if it is a command, check that guest_relay_commands is not disabled
if ($args[2][0] == $this->settings["symbol"] && $this->settings["guest_relay_commands"] != 1) {
	return;
}

if ($type == "priv") {
	//Relay the message to the guild channel
	$msg = "<end>{$this->settings["guest_color_channel"]}[Guest]<end> {$this->settings["guest_color_username"]}".bot::makeLink($sender,$sender,"user")."<end>: {$this->settings["guest_color_guild"]}{$message}<end>";
	bot::send($msg, 'org', true);
} else if ($type == "guild" && count($this->vars["Guest"]) > 0) {
	//Relay the message to the private channel if there is at least 1 char in private channel
	if ($sender == '-1') {
		// for relaying alien attack messages where $sender == -1
		$msg = "<end>{$this->settings["guest_color_channel"]}[{$this -> vars["my guild"]}]<end> {$this->settings["guest_color_guest"]}{$message}<end>";
	} else {
		$msg = "<end>{$this->settings["guest_color_channel"]}[{$this -> vars["my guild"]}]<end> {$this->settings["guest_color_username"]}".bot::makeLink($sender,$sender,"user")."<end>: {$this->settings["guest_color_guest"]}{$message}<end>";
	}
	bot::send($msg, 'prv', true);
}

?>

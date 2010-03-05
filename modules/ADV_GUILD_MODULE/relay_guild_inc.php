<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Handles incoming Guestrelay
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 27.11.2006
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

if($sender == $this->settings["relaybot"]) {
    $msg = substr($message, 4);
    bot::send($msg, "guild", true);

	if($this->settings["guest_relay"] == 1 || (isset($this->vars["guestchannel_enabled"]) && $this->vars["guestchannel_enabled"] && $this->settings["guest_relay"] == 2))
		bot::send($msg, NULL, true);
}
?>

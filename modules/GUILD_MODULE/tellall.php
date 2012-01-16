<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Repeats a message 3times in orgchat or sends a tell to all online orgmembers
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 03.02.2007
   ** Date(last modified): 03.02.2007
   ** 
   ** Copyright (C) 2007 Carsten Lohmann
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

if (preg_match("/^tellall (.+)$/i", $message, $arr)) {
	$data = $db->query("SELECT name FROM online WHERE channel_type = 'guild'");
	forEach ($data as $row) {
		$chatBot->send("Message from $sender: <yellow>".$arr[1]."<end>", $row->name);
	}
	
	$sendto->reply("Your message has been sent to all online org members.");
} else {
	$syntax_error = true;
}

?>
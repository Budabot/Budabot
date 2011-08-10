<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Creates a Doc Assist Macro
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 05.06.2006
   ** Date(last modified): 05.06.2006
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

if (preg_match("/heal$/i", $message)) {
  	if (isset($chatBot->data['heal_assist'])) {
		$link = "<header>::::: Healassist Macro on {$chatBot->data['heal_assist']}:::::\n\n";
	  	$link .= "<a href='chatcmd:///macro {$chatBot->data['heal_assist']} /assist {$chatBot->data['heal_assist']}'>Click here to make a heal assist macro on {$chatBot->data['heal_assist']}</a>";
		$msg = Text::make_blob("Current Healassist is {$chatBot->data['heal_assist']}", $link);
	} else {
		$msg = "No Healassist set atm.";
	}
	$chatBot->send($msg, 'priv');
} else {
	$syntax_error = true;
}
?>
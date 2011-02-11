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

global $heal_assist;
if (preg_match("/heal$/i", $message)) {
  	if (isset($heal_assist)) {
		$link = "<header>::::: Healassist Macro on $heal_assist :::::\n\n";
	  	$link .= "<a href='chatcmd:///macro $heal_assist /assist $heal_assist'>Click here to make an healassist on $heal_assist macro</a>";
		$msg = Text::make_link("Current Healassist is $heal_assist", $link);
	} else {
		$msg = "No Healassist set atm.";
	}
	$chatBot->send($msg, 'priv');
} else if (preg_match("/^heal (.+)$/i", $message, $arr)) {
    $name = $arr[1];
    $uid = AoChat::get_uid(ucfirst(strtolower($name)));
    if ($uid) {
		$name = ucfirst(strtolower($name));
		$heal_assist = $name;
		$link = "<header>::::: HealAssist Macro on $name :::::\n\n";
		$link .= "<a href='chatcmd:///macro $name /assist $name'>Click here to make an healassist on $name macro</a>";
		$msg = Text::make_link("HealAssist Macro on $name", $link);
		$chatBot->send($msg, 'priv');
		$chatBot->send($msg, 'priv');
		$chatBot->send($msg, 'priv');
	} else {
	  	$heal_assist = $name;
		$link = "<header>::::: HealAssist Macro on $name :::::\n\n";
	  	$link .= "<a href='chatcmd:///macro $name /assist $name'>Click here to make an healassist on $name macro</a>";
	  	$msg = Text::make_link("HealAssist Macro on $name", $link);
		$chatBot->send($msg, 'priv');
		$chatBot->send($msg, 'priv');
		$chatBot->send($msg, 'priv');
	}
} else {
	$syntax_error = true;
}
?>
<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Creates a Assist Macro
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 17.12.2006
   ** Date(last modified): 25.02.2006
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

global $caller;
if(eregi("assist$", $message)) {
  	if(isset($caller)) {
		$link = "<header>::::: Assist Macro for $caller :::::\n\n";
	  	$link .= "<a href='chatcmd:///macro $caller /assist $caller'>Click here to make an assist $caller macro</a>";
	  	$msg = bot::makeLink("Current Assist is $caller", $link);
	} else
		$msg = "No caller set atm.";
	bot::reply($type, $sender, $msg);
} elseif(eregi("^assist (.+)$", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
    $uid = AoChat::get_uid($name);
	if($type == "priv" &&!isset($this->chatlist[$name])) {
	  	$msg = "Player <highlight>".$name."<end> isn´t on this bot.";
		bot::reply($type, $sender, $msg);
	}
	
    if(!$uid) {
		$msg = "Player <highlight>".$name."<end> does not exist.";
	    bot::reply($type, $sender, $msg);
	} else {
	  	$caller = $name;
		$link = "<header>::::: Assist Macro for $name :::::\n\n";
	  	$link .= "<a href='chatcmd:///macro $name /assist $name'>Click here to make an assist $name macro</a>";
	  	$msg = bot::makeLink("Assist $name Macro", $link);
		bot::reply($type, $sender, $msg);
		
		if ($type == "priv") {
			bot::reply($type, $sender, $msg);
			bot::reply($type, $sender, $msg);
		}
	}
} else
	$syntax_error = true;
?>
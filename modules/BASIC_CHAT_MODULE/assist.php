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

global $assist;
if(eregi("assist$", $message)) {
  	if(isset($assist)) {
	  	$msg = $assist;
	} else {
		$msg = "No assist set atm.";
	}
	bot::send($msg, $sendto);
} elseif(eregi("^assist (.+)$", $message, $arr)) {
    $nameArray = explode(' ', $arr[1]);
	
	if (count($nameArray) == 1) {
		$name = ucfirst(strtolower($arr[1]));
		$uid = AoChat::get_uid($name);
		if($type == "priv" &&!isset($this->chatlist[$name])) {
			$msg = "Player <highlight>$name<end> isn´t on this bot.";
			bot::send($msg, $sendto);
		}
		
		if(!$uid) {
			$msg = "Player <highlight>$name<end> does not exist.";
			bot::send($msg, $sendto);
		}
		
		$link = "<header>::::: Assist Macro for $name :::::\n\n";
		$link .= "<a href='chatcmd:///macro $name /assist $name'>Click here to make an assist $name macro</a>";
		$msg = bot::makeLink("Assist $name Macro", $link);
		$assist = $msg;
	} else {
		forEach ($nameArray as $key => $name) {
			$name = ucfirst(strtolower($name));
			if($type == "priv" &&!isset($this->chatlist[$name])) {
				$msg = "Player <highlight>$name<end> isn´t on this bot.";
				bot::send($msg, $sendto);
			}
			
			if(!$uid) {
				$msg = "Player <highlight>$name<end> does not exist.";
				bot::send($msg, $sendto);
			}
			$nameArray[$key] = "/assist $name";
		}
		
		// reverse array so that the first player will be the primary assist, and so on
		$nameArray = array_reverse($nameArray);
		$msg = '/macro assist ' . implode(" \\n ", $nameArray);
		$assist = $msg;
	}
	
	if ($msg != '') {
		bot::send($msg, $sendto);
		
		// send message 2 more times (3 total) if used in private channel
		if ($type == "priv") {
			bot::send($msg, $sendto);
			bot::send($msg, $sendto);
		}
	}
} else {
	$syntax_error = true;
}
?>
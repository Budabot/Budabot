<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Adds a player to the raidlist
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 10.10.2006
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

global $raidlist;
if(eregi("^raidadd (.+)$", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    $whois = new whois($name);
    
	if($this->vars["raid_status"] == "") {
	  	$msg = "No raid started!";
	  	bot::send($msg);
	  	return;
	}

	if(!$uid) {
		$msg = "Player <highlight>".$name."<end> does not exist.";
		bot::send($msg);
		return;
	}
	
	if(!isset($this->chatlist[$name])) {
		$msg = "Player <highlight>".$name."<end> needs to be in the bot to be added.";
		bot::send($msg);
		return;	  	
	}
	
	if(isset($raidlist[$name])) {
	  	$msg = "Player <highlight>$name<end> is already in the raidlist.";
	  	bot::send($msg);
	  	return;
	}
	
	$raidlist[$name] = $whois->level;
  	$msg = "Player <highlight>$name<end> has been added to the raidlist.";
  	bot::send($msg);
} else
	$syntax_error = true;
?>
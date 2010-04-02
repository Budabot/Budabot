<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the raidlist
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
if(eregi("^raidlist$", $message)) {
  	if($this->vars["raid_status"] == "") {
		$msg = "No Raid started.";
		if($type == "msg")
			bot::send($msg, $sender);
		else
			bot::send($msg);
		return;
	}
	
  	if(count($raidlist) == 0) {
	  	$msg = "No one is on the raidlist atm.";
	  	if($type == "msg")
			bot::send($msg, $sender);
		else
			bot::send($msg);
	  	return;
	}
	
	ksort($raidlist);

	$list = "<header>::::: Raidlist :::::<end>\n\n";
  	foreach($raidlist as $key => $value) {
  		if(isset($this->admins[$sender]) && $type == "msg") {
    		$kick = bot::makeLink("Raidkick", "/tell <myname> raidkick $key", "chatcmd");
		  	$list .= "<tab>- <highlight>$key<end> ($kick)\n";
		} else
		  	$list .= "<tab>- <highlight>$key<end>\n";
  	}
  	
  	
  	$msg = bot::makeLink("Raidlist", $list);
  	if($type == "msg")
		bot::send($msg, $sender);
	else
		bot::send($msg);
} else
	$syntax_error = true;
?>
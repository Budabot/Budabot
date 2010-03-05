<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Guestchannel (Guest Commands)
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 18.02.2006
   ** Date(last modified): 26.01.2007
   ** 
   ** Copyright (C) 2006, 2007 Carsten Lohmann
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

$type = "priv";
if($this->vars["Guest"][$sender] == true && $this->settings["guest_cmd"] == 1) {
  	if(eregi("^{$this->settings["symbol"]}(kick|leave)$", $message, $arr)) {
		AOChat::privategroup_kick($sender);
	} elseif(eregi("^{$this->settings["symbol"]}(.+)$", $message, $arr)){
		$message 	= $arr[1];
    	$words		= split(' ', strtolower($message));
		$admin 		= $this->guildCmds[$words[0]]["admin level"];
		$filename 	= $this->guildCmds[$words[0]]["filename"];
		
		// Admin Check
		if($admin == "guild"){			
			if($filename != "")
				include $filename;
		} elseif($admin == "all")
			if($filename != "")
				include $filename;
						
		//Shows syntax errors to the user
		if($syntax_error == true)
			bot::send("Syntax error! for more info try /tell <myname> help");
		
		$restricted = true;
	} elseif(eregi("^(afk|brb)", $message, $arr))
		$restricted = false;
} elseif($this->vars["Guest"][$sender] == true && eregi("^{$this->settings["symbol"]}(kick|leave)$", $message))
	AOChat::privategroup_kick($sender);
elseif($this->vars["Guest"][$sender] == true)
	$restricted = true;
?>
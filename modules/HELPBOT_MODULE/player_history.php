<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the history of a player
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.01.2006
   ** Date(last modified): 15.01.2006
   ** 
   ** Copyright (C) 2005 Carsten Lohmann
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

if (preg_match("/^history (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	if (!$chatBot->get_uid($name)) {
		$msg = "Player <highlight>$name<end> doesn't exist.";
	} else {
	  	$msg = "Getting History of player <highlight>$name<end>. Please standby.";
        $chatBot->send($msg, $sendto);
		
		$link = array();
		$history = new history($name);
		if ($history->errorCode != 0) {
			$msg = $history->errorInfo;
		} else {
			$link[] = "<header>::::: History of $name ::::::<end>\n\n";
			$lh = "<highlight>Options:<end>\n";
			$lh .= "<tab><tab><a href='chatcmd:///start $url_orig'>Show History in your browser</a>\n";
			$lh .= "<tab><tab><a href='chatcmd:///tell <myname> alts $name'>Show Alts</a> \n";
			$lh .= "<tab><tab><a href='chatcmd:///tell <myname> whois $name'>Whois</a>\n";
			$lh .= "<tab><tab><a href='chatcmd:///cc addbuddy $name'>Add to your friendslist</a>\n";
			$lh .= "<tab><tab><a href='chatcmd:///cc rembuddy $name'>Remove from your friendslist</a>\n\n";
			
		    $lh .= "Date           Level    AI     Faction      Guild(rank) \n";
		    $lh .= "________________________________________________ \n";
			$l = "";
		    forEach ($history->data as $key => $data) {
		      	$level = $data["level"];
		      	
				if ($data["ailevel"] == "") {
			      	$ailevel = "<green>0<end>";
			    } else {
			    	$ailevel = "<green>".$data["ailevel"]."<end>";
				}
				
				if ($data["faction"] == "Omni") {
			      	$faction = "<omni>Omni<end>";
			    } else if ($data["faction"] == "Clan") {
			      	$faction = "<clan>Clan<end>";
			    } else {
			      	$faction = "<neutral>Neutral<end>";
				}
	
				if ($data["guild"] == "") {
				  	$guild = "Not in a guild";
				} else {
		      		$guild = $data["guild"]."(".$data["rank"].")";
				}
	
			  	$l .= "$key |  $level  | $ailevel | $faction | $guild\n";
			}
			$link[] = array("header" => $lh, "content" => $l);
			$msg = Text::make_link("History of $name", $link);
		}
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
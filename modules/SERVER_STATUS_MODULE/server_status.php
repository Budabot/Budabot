<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows infos about a AO Server
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.01.2006
   ** Date(last modified): 10.04.2006
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

if(eregi("^server(.*)$", $message, $arr)) {
	if(!$arr[1] || $arr[1] == 1 || $arr[1] == 2 || $arr[1] == 3 || $arr[1] == 4) {
	 	$msg = "Getting Server status. Please standby.";
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	   	    bot::send($msg);
	    elseif($type == "guild")
	      	bot::send($msg, "guild");

		$server = new server(trim($arr[1]));
	  	if($server->errorCode != 0)
	  		$msg = $server->errorInfo;
	  	else {
		    $link  = "<header>::::: $server->name Server Status :::::<end>\n\n";

			if($server->servermanager == 1)
				$link .= "<highlight>Servermanager<end> is <green>UP<end>\n";
			else
				$link .= "<highlight>Servermanager<end> is <red>DOWN<end>\n";
				
			if($server->clientmanager == 1)
				$link .= "<highlight>Clientmanager<end> is <green>UP<end>\n";
			else
				$link .= "<highlight>Clientmanager<end> is <red>DOWN<end>\n";

			if($server->chatserver == 1)
				$link .= "<highlight>Chatserver<end> is <green>UP<end>\n\n";
			else
				$link .= "<highlight>Chatserver<end> is <red>DOWN<end>\n\n";
		
	   	    if($arr[1] != 4) {
			    $link .= "<highlight>Faction distribution in % of total players online.<end>\n";
			    $link .= "<blue>Omni<end>: $server->omni%\n";
			    $link .= "<white>Neutral<end>: $server->neutral%\n";
			    $link .= "<red>Clan<end>: $server->clan%\n\n";
			}	    
		    $link .= "<highlight>Player distribution in % of total players online.<end>\n";
   		    ksort($server->data);
		    foreach($server->data as $zone => $proz)
		    	$link .= "<highlight>$zone<end>: {$proz["players"]} \n";
			
			$msg = bot::makeLink("Status of $server->name", $link);	    
		}
	} else
		$msg = "Choose a server between 1 and 4";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
   	    bot::send($msg);
    elseif($type == "guild")
      	bot::send($msg, "guild");
}
?>
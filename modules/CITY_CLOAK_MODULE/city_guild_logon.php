<?php
   /*
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Sends a message to each player about the city status when they logon
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 20-NOV-2007
   ** Date(last modified): 21-DEC-2008
   ** 
   ** Copyright (C) 2008 Jason Wheeler
   **
   ** Licence Infos: 
   ** This file is an addon to Budabot.
   **
   ** This module is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** This module is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with this module; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */

if (isset($this->guildmembers[$sender])) {
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 0, 20 ");
    
    $case = 0;
    if($db->numrows() > 0) 
	{
        $row = $db->fObject();
        if(((time() - $row->time) >= 60*60) && ($row->action == "off")) 
		{
	        $case = 1;
            $msg = "The cloaking device is disabled. It is possible to enable it.";
    	} 
		elseif(((time() - $row->time) < 60*30) && ($row->action == "off")) 
		{
	    	$case = 1;
            $msg = "<red>RAID IN PROGRESS!  DO NOT ENTER CITY!</red>";
    	} 
		elseif(((time() - $row->time) < 60*60) && ($row->action == "off")) 
		{
            $msg = "Cloaking device is disabled. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to enable it.";
            $case = 1;
    	} 
		elseif(((time() - $row->time) >= 60*60) && ($row->action == "on")) 
		{
            $msg = "The cloaking device is enabled. It is possible to disable it.";
            $case = 2;
    	} 
		elseif(((time() - $row->time) < 60*60) && ($row->action == "on")) 
		{
            $msg = "The cloaking device is <green>enabled<end>. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to disable it.";
            $case = 2;
    	} 
		else 
		{
			$msg = "<highlight>Unknown status on city cloak!<end>";
			$case = 1;
		}
		if ( $case <= $this->settings["showcloakstatus"])
		{
			bot::send($msg, $sender);
		}
    }
}

?>
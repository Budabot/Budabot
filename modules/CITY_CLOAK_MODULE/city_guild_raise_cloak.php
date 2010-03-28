<?php
   /*
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Shows gas changes for tower sites
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

{
	// valid states for action are: 'on', 'off', 'Attack'
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 1 ");

    if($db->numrows() != 0)
    {
	    $msg = "";
        $row = $db->fObject();
        
        if ($row->action == "off")
        {
	        // 10 minutes before, send tell to player
	        if ((time() - $row->time) >= 49*60 && (time() - $row->time) <= 50*60)
	        {
	            $msg = "The cloaking device is disabled. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to enable it.";
            }
            // 1 minute before send tell to player
	        else if ((time() - $row->time) >= 58*60 && (time() - $row->time) <= 59*60)
	        {
	            $msg = "The cloaking device is disabled. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to enable it.";
            }
            // when cloak can be raised, send tell to player and
            // every 5 minutes after, send tell to player (and message in org chat)
	        else if ((time() - $row->time) >= 59*60 && ((time() - $row->time) % (60*5) >= 0 && (time() - $row->time) % (60*5) <= 60 ) )
	        {
	        	$msg = "The cloaking device is disabled. Please enable it now.";
        	}
	
	        if ($msg)
	        {
		        // send message to main if he/she is online
		        if($this->buddyList[$row->player] == 1)
		        {
					bot::send($msg, $row->player);
				}
				
				// send message to any online alts
		        $db->query("SELECT * FROM `alts` WHERE `main` = (SELECT `main` FROM `alts` WHERE `main` = '$row->player' or `alt` = '$row->player' LIMIT 1)");
		        while($nextAlt = $db->fObject())
		        {
			        if($this->buddyList[$nextAlt->alt] == 1)
			        {
						bot::send($msg, $nextAlt->alt);
					}
				}
			}
	        	
			// send message to org chat every 5 minutes that the cloaking device is
			// disabled past the the time that the cloaking device could be enabled.
			$interval = 5;
			if ((time() - $row->time) >= 65*60 && ((time() - $row->time) % (60 * $interval) >= 0 && (time() - $row->time) % (60 * $interval) <= 60 ))
        	{
	        	bot::send("The cloaking device is disabled. It is possible to enable it.", 'guild');
        	}
    	}
    }
}

?>
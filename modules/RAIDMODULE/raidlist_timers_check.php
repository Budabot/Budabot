<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Checks if a player needs to be removed from the raidlist(after he left the privgrp)
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 03.03.2007
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

global $raidlist;
global $raidlist_timers;

$time = time();
foreach($raidlist_timers as $key => $value) {
	$left_time = $time - $value;
  	if(($left_time >= 120) && ($left_time < 180))
  		bot::send("You have left the bot for more than 2minutes now and will be kicked of the raidlist soon.", $key);
  	elseif($left_time >= 300) {
	    bot::send("You have been kicked out of the raidlist.", $key);
	    if(isset($raidlist[$key]))
			unset($raidlist[$key]);

	    if(isset($raidlist_timers[$key]))			
		    unset($raidlist_timers[$key]);
	}
}
?>
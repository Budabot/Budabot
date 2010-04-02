<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Leting a player join for the city raidhoster roll
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.08.2006
   ** Date(last modified): 17.08.2006
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

global $aihoster;
if(eregi("^crhadd$", $message)) {
  	if($aihoster == "") {
	    $msg = "No hoster roll started!";
	    bot::send($msg);
	    return;	  		    
	}
  
	if(isset($aihoster["list"][$sender])) {
	    $msg = "<highlight>$sender<end> has been added already!";
	    bot::send($msg);
	    return;	  	
	}
	
	if(!isset($this->guildmembers[$sender])) {
	    $msg = "Only Guildmembers can add!";
	    bot::send($msg);
	    return;	  	
	}
	
	$aihoster["list"][$sender] = true;
	
	bot::send("<highlight>$sender<end> has been added to the City Raid Hosterlist.");
} else
	$syntax_error = true;
?>
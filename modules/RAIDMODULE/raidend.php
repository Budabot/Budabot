<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Ends a raid
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
global $raid_points;
global $raidloot;
if(eregi("^raidend$", $message)) {
	if($this->vars["raid_status"] == "") {
		$msg = "No Raid started.";
		bot::send($msg);
		return;
	}  
  
  	$msg = "Raid for <highlight>{$this->vars["raid_status"]}<end> has ended.";
  	bot::send($msg);
  	$this->vars["raid_status"] = "";
  	$raidlist = "";
  	bot::savesetting("topic_time", time());
  	bot::savesetting("topic_setby", $sender);
  	bot::savesetting("topic", "No Topic set atm.");	
	$raid_points = false;
	$raidloot = "";
} else
	$syntax_error = true;
?>
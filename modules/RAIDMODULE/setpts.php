<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Sets the amount of points for a player
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 31.01.2007
   ** Date(last modified): 31.01.2007
   ** 
   ** Copyright (C) 2007 Carsten Lohmann
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

if(eregi("^setpoints ([a-z0-9]+) ([0-9]+)$", $message, $arr)) {
 	$who = ucfirst(strtolower($arr[1]));
 	$pts = $arr[2];
 	
	if(AOChat::get_uid($who) == NULL){
		bot::send("<red>Sorry the player doesn´t exist.<end>", $sender);
		return;	
	}

	if($pts < 0 || $pts > 2000)	{
		bot::send("<red>You need to enter a valid points amount(0-2000)<end>", $sender);
		return;
	}

	$db->query("SELECT * FROM `points_db_<myname>` WHERE `name` = '$who'");
	if($db->numrows() == 0)
		$db->query("INSERT INTO `points_db_<myname>` VALUES ('$who', $pts)");
	else
		$db->query("UPDATE `points_db_<myname>` SET `points` = $pts WHERE `name` = '$who'");

	bot::send("The Player <highlight>$who<end> has now <highlight>$pts<end>points.", $sender);	
} else
	$syntax_error = true;
?>
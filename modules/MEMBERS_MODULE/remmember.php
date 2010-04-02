<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Remove a member
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.04.2006
   ** Date(last modified): 04.04.2006
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
   
if(eregi("^remmember (.+)$", $message, $arr)) {
 	$newmember = ucfirst(strtolower($arr[1]));
  	$uid = AoChat::get_uid($bot);
    if(!$uid) {
        $msg = "Player <highlight>".$newmember."<end> does not exist.";
		if($type == "msg")
		    bot::send($msg, $sender);
		else
			bot::send($msg);
		return;      	
    }
    
    $db->query("SELECT * FROM members_<myname> WHERE `name` = '$newmember'");
    if($db->numrows() == 0) {
        $msg = "Player <highlight>".$newmember."<end> isn´t a member of this bot.";
		if($type == "msg")
		    bot::send($msg, $sender);
		else
			bot::send($msg);
		return;     
	}
	
	$db->query("DELETE FROM members_<myname> WHERE `name` = '$newmember'");
	bot::send("rembuddy", $newmember);
	$msg = "You have successfull removed <highlight>$newmember<end> as member of this bot.";
	if($type == "msg")
	    bot::send($msg, $sender);
	else
		bot::send($msg);
		
	$msg = "You have been removed as a member of <highlight>{$this->vars["name"]}<end>.";
    bot::send($msg, $newmember);
} else
	$syntax_error = true;
?>
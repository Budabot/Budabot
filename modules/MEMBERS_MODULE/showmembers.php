<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Show the current members
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.04.2006
   ** Date(last modified): 21.11.2006
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
   
if(eregi("^members$", $message)) {
  	$query = "SELECT * FROM members_<myname> ORDER BY `name`";
	$list = "<header>::::: Members of this bot :::::<end>\n\n";

  	$db->query($query);
  	$num = $db->numrows();
  	if($num == 0) {
	    $msg = "I have no members yet sry.";
		if($type == "msg")
		    bot::send($msg, $sender);
		else
			bot::send($msg);
	    return;
	}
	
	$msg = "Processing Memberslist. This can take a few seconds.";
	if($type == "msg")
	    bot::send($msg, $sender);
	else
		bot::send($msg);
			
	while($row = $db->fObject()) {
 	  	if($this->buddyList[$row->name] == 1) {
			$status = "<green>Online";
			if($this->chatlist[$row->name] == true)
		    	$status .= " and in Channel";
		} else
			$status = "<red>Offline";

  		$list .= "<tab>- $row->name ($status<end>)\n";
	}
	
	$msg = bot::makeLink("$num Members in total", $list);
	if($type == "msg")
	    bot::send($msg, $sender);
	else
		bot::send($msg);
}
?>
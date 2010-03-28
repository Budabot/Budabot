<?
   /*
   ** Author: Tyrence (RK2)
   ** Description: Add tower sites to watch list
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2007
   ** Date(last modified): 23.11.2007
   ** 
   ** Copyright (C) 2007 Jason Wheeler
   **
   ** Licence Infos: 
   ** This file is module for of Budabot.
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

if(eregi("^notes$", $message)) {

	$moreInfoMsg = "";

	$sql = "SELECT * FROM notes_<myname> WHERE name LIKE '$sender'";
  	$db->query($sql);
  	while($note = $db->fObject()) {
		
	  	$remove = bot::makeLink('Remove', "/tell <myname> <symbol>note rem $note->id" , 'chatcmd');
	  	$moreInfoMsg .= "$remove $note->note\n\n";
	}
	
	if ($moreInfoMsg == "") {
		$moreInfoMsg = "No notes.";	
	}
	
	
	$moreInfoMsg = "Notes for $sender\n\n" . $moreInfoMsg;
	
	$msg = bot::makeLink('Notes', $moreInfoMsg, 'blob');
  	

    if($type == "msg") {
        bot::send($msg, $sender);
    } else if($type == "priv") {
       	bot::send($msg);
   	} else if($type == "guild") {
       	bot::send($msg, "guild");
   	}
}
?>

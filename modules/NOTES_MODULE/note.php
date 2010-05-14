<?php
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

if (preg_match("/^note( (.*))?$/i", $message)) {

	$usage = "Usage:\n<symbol>note add &lt;note&gt;\n<symbol>note rem &lt;note_id&gt;";
	$msg = "";

  	if (eregi("^note (rem|add) (.*)$", $message, $arr)) {
	  	$action = strtolower($arr[1]);
	  	$parm2 = $arr[2];
  	
	  	// if side isn't omni, neutral or clan
	  	if ($action == 'rem') {
			$numRows = $db->query("DELETE FROM notes_<myname> WHERE id = $parm2 AND name LIKE '$sender'");
			
			if ($numRows) {
			  	$msg = "Note deleted successfully.";
		  	} else {
			  	$msg = "Note could not be found.";
  			}
	  	} else if ($action == 'add') {
		  	$note = str_replace("'", "''", $parm2);
		  	
		  	$query = "INSERT INTO notes_<myname> (name, note) VALUES('$sender', '$note')";
		  	$db->query($query);
		  	$msg = "Note added successfully.";
  		} else {
	  		$msg = $usage;		
  		}
	} else {
		$msg = $usage;
	}

    bot::send($msg, $sendto);
}
?>

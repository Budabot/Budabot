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

if (preg_match("/^notes?$/i", $message)) {
	$blob = "<header> :::::: Notes for $sender :::::: <end>\n\n";

	$sql = "SELECT * FROM notes_<myname> WHERE name LIKE '$sender'";
  	$db->query($sql);
	$data = $db->fObject('all');
  	forEach ($data as $row) {
	  	$remove = Text::make_link('Remove', "/tell <myname> <symbol>note rem $note->id" , 'chatcmd');
	  	$blob .= "$remove $note->note\n\n";
	}
	
	if (count($data) == 0) {
		$msg = "No notes for $sender.";	
	} else {
		$msg = Text::make_link("Notes for $sender", $blob, 'blob');
	}
  	
	bot::send($msg, $sendto);
} else if (preg_match("/^notes? (rem|add) (.*)$/i", $message, $arr)) {
	$action = strtolower($arr[1]);
	$parm2 = $arr[2];

	if ($action == 'rem') {
		$numRows = $db->exec("DELETE FROM notes_<myname> WHERE id = $parm2 AND name LIKE '$sender'");
		
		if ($numRows) {
			$msg = "Note deleted successfully.";
		} else {
			$msg = "Note could not be found.";
		}
	} else if ($action == 'add') {
		$note = str_replace("'", "''", $parm2);

		$db->exec("INSERT INTO notes_<myname> (name, note) VALUES('$sender', '$note')");
		$msg = "Note added successfully.";
	}
    bot::send($msg, $sendto);
}

?>

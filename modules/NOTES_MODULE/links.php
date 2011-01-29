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

if (preg_match("/^links$/i", $message)) {
	$blob = "<header> :::::: Links :::::: <end>\n\n";

	$sql = "SELECT * FROM links ORDER BY dt DESC";
  	$db->query($sql);
	$data = $db->fObject('all');
  	forEach ($data as $row) {
	  	$remove = bot::makeLink('Remove', "/tell <myname> <symbol>links rem $row->id" , 'chatcmd');
		$website = bot::makeLink($row->website, "/start $row->website", 'chatcmd');
		$dt = gmdate("M j, Y, G:i", $row->dt);
	  	$blob .= "$website <white>$row->comments<end> [<green>$row->name<end>] <white>$dt<end> $remove\n";
	}
	
	if (count($data) == 0) {
		$msg = "No links found.";	
	} else {
		$msg = bot::makeLink('Links', $blob, 'blob');
	}
  	
	bot::send($msg, $sendto);
} else if (preg_match("/^links add ([^ ]+) (.+)$/i", $message, $arr)) {
	$website = str_replace("'", "''", $arr[1]);
	$comments = str_replace("'", "''", $arr[2]);

	$db->query("INSERT INTO links (`name`, `website`, `comments`, `dt`) VALUES('$sender', '$website', '$comments', '" . time() . "')");
	$msg = "Link added successfully.";
    bot::send($msg, $sendto);
} else if (preg_match("/^links rem ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];

	$numRows = $db->exec("DELETE FROM links WHERE id = $id AND name LIKE '$sender'");
	if ($numRows) {
		$msg = "Link deleted successfully.";
	} else {
		$msg = "Link could not be found or was not submitted by you.";
	}
    bot::send($msg, $sendto);
}

?>

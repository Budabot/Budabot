<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Sets/shows/deletes News entries
   ** Version: 0.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.01.2006
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

if (preg_match("/^news del ([0-9]+)$/i", $message, $arr)) {
	$rows = $db->exec("DELETE FROM news WHERE `id` = {$arr[1]}");
	if ($rows == 0) {
		$msg = "No news entry found with the ID <highlight>{$arr[1]}<end>.";
	} else {
		$msg = "News entry with the ID <highlight>{$arr[1]}<end> was successfully deleted.";
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
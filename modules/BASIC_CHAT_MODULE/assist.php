<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Creates a Assist Macro
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 17.12.2006
   ** Date(last modified): 25.02.2006
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

global $caller;
if(eregi("assist$", $message)) {
  	if(isset($caller)) {
	  	$msg = $caller;
	} else {
		$msg = "No caller set atm.";
	}
	bot::send($msg, $sendto);
} elseif(eregi("^assist (.+)$", $message, $arr)) {
    $nameArray = explode(' ', $arr[1]);
	
	forEach ($nameArray as $key => $name) {
		$nameArray[$key] = "/assist $name";
	}
	$msg = '/macro assist ' . implode(" \\n ", $nameArray);
	bot::send($msg, $sendto);
} else
	$syntax_error = true;
?>
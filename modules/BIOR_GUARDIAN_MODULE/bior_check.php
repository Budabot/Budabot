<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Checks the running BioR Perks
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 24.02.2006
   ** Date(last modified): 26.02.2006
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

foreach($bior as $key => $value) {
	if ($chatBot->data['bior'][$key]["b"] != "ready") {
	  	$rem = $chatBot->data['bior'][$key]["b"] - time();
	  	if ($rem >= 319 && $rem <= 321) {
	  		$msg = "<blue>20sec remaining on Bio Regrowth.<end>";
	  		$chatBot->send($msg, $sendto);
	  	} else if ($rem >= 305 && $rem <= 307) {
	  	  	$pos = array_search($key, $chatBot->data['blist']);
	  	  	if (isset($chatBot->data['blist'][$pos + 1])) {
	  	  		$next = " <yellow>Next is {$chatBot->data['blist'][$pos + 1]}<end>";
			}
	  		$msg = "<blue>6sec remaining on Bio Regrowth.$next<end>";  		
	  		$chatBot->send($msg, $sendto);
	  	} else if ($rem >= 299 && $rem <= 301) {
	  	  	$pos = array_search($key, $chatBot->data['blist']);
	  	  	if (isset($chatBot->data['blist'][$pos + 1])) {
	  	  		$next = " <yellow>Next is {$chatBot->data['blist'][$pos + 1]}<end>";
			}
	  		$msg = "<blue>Bio Regrowth has terminated.$next<end>";
	  		$chatBot->send($msg, $sendto);
	  	} else if ($rem <= 0) {
	  		$msg = "<blue>Bio Regrowth is ready on $key.<end>";
	  		$chatBot->data['bior'][$key]["b"] = "ready";
	  		$chatBot->send($msg, $sendto);
	  	}
	}
}
?>
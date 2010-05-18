<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Updates the character infos
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 05.06.2006
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

if(preg_match("/^updateme$/i", $message)) {
  	$rk_num = $this->vars["dimension"];
  	$cache = $this->vars["cachefolder"];
  	if(file_exists("$cache/$sender.$rk_num.xml")) {
	    if(!unlink("$cache/$sender.$rk_num.xml")) {
		  	bot::send("An Error occurred while trying to update your infos. Please try again laters.", $sender);
		  	return;
		}
	    
	    $info = new whois($sender);
	    if($info->errorCode != 0) {
		  	bot::send("An Error occurred while trying to update your infos. Please try again laters.", $sender);
		  	return;
		}
		
		$db->query("SELECT * FROM priv_chatlist_<myname> WHERE `name` = '$sender'");

		if($db->numrows() != 0)
		    $db->query("UPDATE priv_chatlist_<myname> SET `faction` = '{$info->faction}', `profession` = '{$info->prof}', `guild` = '{$info->org}', `breed` = '{$info->breed}', `level` = {$info->level}, `ai_level` = {$info->ai_level} WHERE `name` = '$sender'");

		bot::send("Update successfull.", $sender);
	} else
		bot::send("No update needed.", $sender);
} else
	$syntax_error = true;
?>
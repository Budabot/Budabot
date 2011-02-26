<?php
   /*
   ** Author: Sebuda, Derroylo (RK2)
   ** Description: Shows the adminlist of the bot
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 30.01.2007
   ** 
   ** Copyright (C) 2005, 2006, 2007 J. Gracik, C. Lohmann
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

if (preg_match("/^adminlist$/i", $message) || preg_match("/^admins$/i", $message)) {
	$list = "<header>::::: Adminlist :::::<end>\n\n";

	$list .= "<highlight>Administrators<end>\n";	
	forEach ($chatBot->admins as $admin){
		if ($admin->access_level == 4){
			$list.= "<tab>$admin->name ";
			
			if ($chatBot->vars["SuperAdmin"] == $admin->name) {
				$list .= "(<orange>Super Administrator<end>) ";
			}
			
			$online = Buddylist::is_online($admin->name);
				
			if (isset($chatBot->chatlist[$admin->name])) {
				$list.="(<green>Online and in chat<end>)";
			} else if ($online == 1) {
				$list.="(<green>Online<end>)";
			} else if ($online === null) {
				$list.="(<orange>Unknown<end>)";
			} else {
				$list.="(<red>Offline<end>)";
			}
				
			$list.= "\n";
		}
	}

	$list .= "<highlight>Moderators<end>\n";
	forEach ($chatBot->admins as $admin){
		if ($admin->access_level == 3){
			$list.= "<tab>$admin->name ";
			
			$online = Buddylist::is_online($admin->name);
				
			if (isset($chatBot->chatlist[$admin->name])) {
				$list.="(<green>Online and in chat<end>)";
			} else if ($online == 1) {
				$list.="(<green>Online<end>)";
			} else if ($online === null) {
				$list.="(<orange>Unknown<end>)";
			} else {
				$list.="(<red>Offline<end>)";
			}
				
			$list.= "\n";
		}
	}

	$list .= "<highlight>Raidleaders<end>\n";	
	forEach ($chatBot->admins as $admin){
		if ($admin->access_level == 2){
			$list.= "<tab>$admin->name ";
			
			$online = Buddylist::is_online($admin->name);
				
			if (isset($chatBot->chatlist[$admin->name])) {
				$list.="(<green>Online and in chat<end>)";
			} else if ($online == 1) {
				$list.="(<green>Online<end>)";
			} else if ($online === null) {
				$list.="(<orange>Unknown<end>)";
			} else {
				$list.="(<red>Offline<end>)";
			}
				
			$list.= "\n";
		}
	}

	$link = Text::make_link('Bot Administrators', $list);	
	$chatBot->send($link, $sendto);
} else {
	$syntax_error = true;
}

?>
<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: General Help/Shows all helpfiles
   ** Version: 0.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 21.11.2006
   **
   ** Copyright (C) 2005, 2006 J. Gracik
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

if (preg_match("/^about$/i", $message)) {
	$data = file_get_contents("./core/HELP/about.txt");
	$msg = bot::makeLink("About", $data);
	bot::send($msg, $sendto);
} else if (preg_match("/^help$/i", $message)) {
	global $version;
	$data .= "\nBudabot version: $version\n\n";
	ksort($this->helpfiles);
	forEach ($this->helpfiles as $key => $file){
		$access = false;
		$admin_level = $file["admin level"];
		$user_admin_level = $this->getUserAdminLevel($sender);
		if ($user_admin_level <= $admin_level) {
			if ($access && $file["info"] != "") {
				$list .= "  *{$file["info"]} <a href='chatcmd:///tell <myname> help $key'>Click here</a>\n";
			} else if ($access) {
				$list .= "  *Basic Help. <a href='chatcmd:///tell <myname> help $key'>Click here</a>\n";
			}
		}
	}
	if ($list == "") {
		$msg = "<red>No Helpfiles found.<end>";
	} else {
		$msg = bot::makeLink("Help(main)", $data.$list);
	}
	bot::send($msg, $sendto);
} else if (preg_match("/^help (.+)$/i", $message, $arr)) {
	if (($output = $this->help_lookup($sender, $arr[1])) !== FALSE) {
		bot::send($output, $sendto);
	} else {
		bot::send("No help found on this topic.", $sendto);
	}
}

?>
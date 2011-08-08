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

if (preg_match("/^about$/i", $message) || preg_match("/^help about$/i", $message)) {
	global $version;
	$data = file_get_contents("./core/HELP/about.txt");
	$data = str_replace('<version>', $version, $data);
	$msg = Text::make_link("About Budabot", $data);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^help$/i", $message)) {
	global $version;

	$sql = "SELECT * FROM hlpcfg_<myname> ORDER BY module ASC";
	$db->query($sql);
	$data = $db->fObject('all');
	
	$help_array = array();
	forEach ($data as $row) {
		if (AccessLevel::checkAccess($sender, $row->admin)) {
			$help_array []= $row;
		}
	}

	if (count($help_array) == 0) {
		$msg = "<orange>No Helpfiles found.<end>";
	} else {
		$blob = array(array("content" => "<header> :::: Help Files for Budabot {$version} ::: <end>\n\n"));
		$current_module = '';
		$current_content = '';
		forEach ($help_array as $row) {
			if ($current_module != $row->module) {
				
				if ($current_module != '') {
					$blob[] = array("header" => "<highlight><u>{$row->module}:</u><end>\n", "content" => $current_content, "footer" => "\n");
				}
				$current_module = $row->module;
				$current_content = '';
			}
			
			$current_content .= "  *{$row->name}: {$row->description} <a href='chatcmd:///tell <myname> help {$row->name}'>Click here</a>\n";
		}
		
		$blob[] = array("header" => "<highlight><u>{$row->module}:</u><end>\n", "content" => $current_content);
		$msg = Text::make_link("Help(main)", $blob, 'blob');
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^help (.+)$/i", $message, $arr)) {
	$output = Help::find($arr[1], $sender);
	if ($output !== false) {
		$chatBot->send($output, $sendto);
	} else {
		$chatBot->send("No help found on this topic.", $sendto);
	}
}

?>
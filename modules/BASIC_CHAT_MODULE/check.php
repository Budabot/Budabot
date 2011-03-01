<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Check who of the players in chat are in the area
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 08.03.2006
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
   
if (preg_match("/^check$/i", $message) || preg_match("/^check all$/i", $message)) {
	$list = "<header>::::: Check for all members :::::<end>\n\n";
	$db->query("SELECT name FROM online WHERE added_by = '<myname>' AND channel_type = 'priv'");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$content .= " \\n /assist $row->name";
	}

	$list .= "<a href='chatcmd:///text AssistAll: $content'>Click here to check who is here</a>";
	$msg = Text::make_link("Check on all", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^check prof$/i", $message)) {
	$list = "<header>::::: Check for all professions :::::<end>\n\n";
	$db->query("SELECT o.name, p.profession FROM online o LEFT JOIN players p ON o.name = o.name WHERE added_by = '<myname>' AND channel_type = 'priv' ORDER BY `profession` DESC");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$prof[$row->profession] .= " \\n /assist $row->name";
	}

	ksort($prof);
	
	forEach ($prof as $key => $value) {
		$list .= "<a href='chatcmd:///text Assist $key: $value'>Click here to check $key</a>\n";
	}

	$msg = Text::make_link("Check on professions", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^check org$/i", $message)) {
	$list = "<header>::::: Check for all organizations :::::<end>\n\n";
	$db->query("SELECT o.name, p.guild FROM online o LEFT JOIN players p ON o.name = p.name WHERE added_by = '<myname>' AND channel_type = 'priv' ORDER BY `guild` DESC");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		if ($row->guild == "") {
			$org["Non orged"] .= " \\n /assist $row->name";
		} else {
			$org[$row->guild] .= " \\n /assist $row->name";
		}
	}
	
	ksort($org);
	
	forEach ($org as $key => $value) {
		$list .= "<a href='chatcmd:///text Assist $key: $value'>Click here to check $key</a>\n";
	}

	$msg = Text::make_link("Check on Organizations", $list);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
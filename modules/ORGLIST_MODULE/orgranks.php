<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Show Orgmembers sorted after ranks
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 06.11.2006
   ** Date(last modified): 26.11.2006
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
   
if (preg_match("/^orgranks$/i", $message)) {
	if ($chatBot->vars["my_guild_id"] == "") {
	  	$msg = "The Bot needs to be in a org to show the orgmembers.";
        $chatBot->send($msg, $sendto);
	}
	
	$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.name = p.name WHERE `mode` != 'del' ORDER BY `guild_rank_id`");
	$members = $db->numrows();
  	if ($members == 0) {
	  	$msg = "No members recorded.";
        $chatBot->send($msg, $sendto);
	}

	$msg = "Getting guild info. Please wait...";
    $chatBot->send($msg, $sendto);
       	
	$list = "<header> :::::: Members of <myguild> (Sorted by org rank) :::::: <end>\n\n";
	$data = $db->fObject('all');
	forEach ($data as $row) {
		if (Buddylist::is_online($row->name) == 1) {
			$logged_off = "<green>Online<end>";
        } else if ($row->logged_off != "0") {
	        $logged_off = gmdate("l F d, Y - H:i", $row->logged_off)."(GMT)";
	    } else {
	    	$logged_off = "<orange>Unknown<end>";
		}
	    	
	  	$list .= "<tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end> $row->profession) (<highlight>$row->guild_rank<end>) <highlight>::<end> Last logoff: $logged_off\n";
	}
	
	$msg = Text::make_link("<myguild> members by org rank ($members)", $list);
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^orgranks ([0-9]+)$/i", $message, $arr1) || preg_match("/^orgranks ([a-z0-9-]+)$/i", $message, $arr2)) {
	if ($arr2) {
		// Someone's name.  Doing a whois to get an orgID.
		$name = ucfirst(strtolower($arr2[1]));
		$whois = Player::get_by_name($name);

		if ($whois === null) {
			$msg = "Could not find character info for $name.";
			$chatBot->send($msg, $sendto);
			return;
		} else if (!$whois->guild_id) {
			$msg = "Player <highlight>$name<end> does not seem to be in any org.";
			$chatBot->send($msg, $sendto);
			return;
		} else {
			$guild_id = $whois->guild_id;
		}
	} else {
		$guild_id = $arr1[1];
	}

  	$msg = "Getting guild info. Please wait...";
    $chatBot->send($msg, $sendto);
	
    $org = Guild::get_by_id($guild_id);
	if ($org === null) {
		$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$sql = "SELECT * FROM players WHERE guild_id = {$guild_id} ORDER BY guild_rank_id ASC, name ASC";
	$db->query($sql);
	
	$blob = "{$org->orgname} has {$db->numrows()} members.\n";
	
	$data = $db->fObject('all');
	$current_rank_id = '';
	forEach ($data as $row) {
		if ($current_rank_id != $row->guild_rank_id) {
			$current_rank_id = $row->guild_rank_id;
			$blob .= "\n<white>{$row->guild_rank}\n";
		}
		
		$blob .= "<tab><highlight>{$row->name} (Level {$row->level}";
		if ($row->ai_level > 0) {
			$blob .= "<green>/{$row->ai_level}<end>";
		}
		$blob .= ", {$row->gender} {$row->breed} {$row->profession})<end>\n";
	}
	
	$msg = Text::make_link("Org ranks for '$org->orgname'", $blob, 'blob');
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Handles Guestrelay Member Logon
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
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

if ($this->settings["relaybot"] != "Off" && isset($this->guildmembers[$sender])) {
    $whois = Player::get_by_name($sender);
	
	$msg = '';
	if ($whois === null) {
		$msg = "$sender logged on.";
	} else {
		$msg = Player::get_info($whois);

        $msg .= " logged on.";

        // Alternative Characters Part
        $main = false;
        // Check if $sender is the main
        $db->query("SELECT * FROM alts WHERE `main` = '$sender'");
        if ($db->numrows() == 0) {
            // Check if $sender is an alt
            $db->query("SELECT * FROM alts WHERE `alt` = '$sender'");
            if ($db->numrows() != 0) {
                $row = $db->fObject();
                $main = $row->main;
            }
        } else {
            $main = $sender;
		}

        $blob = Alts::get_alts_blob($sender);

		if ($main != $sender && $main != false) {
			$alts = bot::makeLink("Alts", $blob);
			$msg .= " Main: <highlight>$main<end> ($alts)";
		} else if ($main != false) {
  			$alts = bot::makeLink("Alts of $main", $blob);
			$msg .= " $alts";
		}

		$sql = "SELECT logon_msg FROM org_members_<myname> WHERE name = '{$sender}'";
		$db->query($sql);
		$row = $db->fObject();
        if ($row !== null && $row->logon_msg != '') {
            $msg .= " - " . $row->logon_msg;
		}

		send_message_to_relay("grc <grey>[".$this->vars["my guild"]."] ".$msg);
    }
}
?>

<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows logon from Guildmembers
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 26.11.2006
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

$msg = "";
$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.name = p.name WHERE o.`name` = '$sender'");
$numrows = $db->numrows();
$org_member = $db->fObject();
if ($org_member->mode != "del" && $numrows == 1) {
  	$db->query("SELECT name FROM guild_chatlist_<myname> WHERE `name` = '$sender'");
	if ($db->numrows() == 0) {
	    $db->exec("INSERT INTO guild_chatlist_<myname> (`name`) VALUES ('$org_member->name')");
	}

    if (time() >= $this->vars["onlinedelay"]) {
        if ($org_member->firstname) {
            $msg = $org_member->firstname." ";
		}

        $msg .= "<highlight>\"{$org_member->name}\"<end> ";

        if ($org_member->lastname) {
            $msg .= $org_member->lastname." ";
		}

        $msg .= "(Level <highlight>{$org_member->level}<end>/<green>{$org_member->ai_level} - {$org_member->ai_rank}<end>, {$org_member->gender} {$org_member->breed} <highlight>{$org_member->profession}<end>,";

        if ($org_member->guild) {
            $msg .= " {$org_member->guild_rank} of <highlight>{$org_member->guild}<end>) ";
        } else {
            $msg .= " Not in a guild.) ";
		}

        $msg .= "logged on. ";

        // Alternative Characters Part
        $main = false;
        // Check if $sender is hisself the main
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

        // If a main was found create the list
        if ($main) {
            $list = "<header>::::: Alternative Character List :::::<end> \n \n";
            $list .= ":::::: Main Character\n";
            $list .= "<tab><tab>".bot::makeLink($main, "/tell ".$this->vars["name"]." whois $main", "chatcmd")." - ";
            $online = $this->buddy_online($main);
            if ($online === null) {
                $list .= "No status.\n";
            } else if ($online == 1) {
                $list .= "<green>Online<end>\n";
            } else { // if ($online == 0)
                $list .= "<red>Offline<end>\n";
			}

            $list .= ":::::: Alt Character(s)\n";
            $db->query("SELECT * FROM alts WHERE `main` = '$main'");
            while ($row = $db->fObject()) {
                $list .= "<tab><tab>".bot::makeLink($row->alt, "/tell ".$this->vars["name"]." whois $row->alt", "chatcmd")." - ";
                $online = $this->buddy_online($row->alt);
                if ($online === null) {
                    $list .= "No status.\n";
                } else if ($online == 1) {
                    $list .= "<green>Online<end>\n";
                } else { // if ($online == 0)
                    $list .= "<red>Offline<end>\n";
				}
            }
        }

		if ($main != $sender && $main != false) {
			$alts = bot::makeLink("Alts", $list);
			$msg .= "Main: <highlight>$main<end> ($alts) ";
		} else if ($main != false) {
  			$alts = bot::makeLink("Alts of $main", $list);
			$msg .= "$alts ";
		}

        if ($org_member->logon_msg != '0') {
            $msg .= " - " . $org_member->logon_msg;
		}

       	bot::send($msg, "guild", true);

		//Guestchannel part
		if ($this->settings["guest_relay"] == 1) {
			bot::send($msg, "priv", true);
		}
		
		// update info for player
		Player::get_by_name($sender);
    }
}
?>

<?php

if (isset($this->guildmembers[$sender]) && time() >= $this->vars["onlinedelay"] && $this->settings["bot_notify"] != 0) {
	$org_member = Player::get_by_name($sender);
	
	$msg = '';
	if ($org_member === null) {
		$msg = "$sender logged on.";
	} else {
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
            $blob = "<header>::::: Alternative Character List :::::<end> \n \n";
            $blob .= ":::::: Main Character\n";
            $blob .= "<tab><tab>".bot::makeLink($main, "/tell ".$this->vars["name"]." whois $main", "chatcmd")." - ";
            $online = $this->buddy_online($main);
            if ($online === null) {
                $blob .= "No status.\n";
            } else if ($online == 1) {
                $blob .= "<green>Online<end>\n";
            } else { // if ($online == 0)
                $blob .= "<red>Offline<end>\n";
			}

            $blob .= ":::::: Alt Character(s)\n";
            $db->query("SELECT * FROM alts WHERE `main` = '$main'");
            while ($row = $db->fObject()) {
                $blob .= "<tab><tab>".bot::makeLink($row->alt, "/tell ".$this->vars["name"]." whois $row->alt", "chatcmd")." - ";
                $online = $this->buddy_online($row->alt);
                if ($online === null) {
                    $blob .= "No status.\n";
                } else if ($online == 1) {
                    $blob .= "<green>Online<end>\n";
                } else { // if ($online == 0)
                    $blob .= "<red>Offline<end>\n";
				}
            }
        }

		if ($main != $sender && $main != false) {
			$alts = bot::makeLink("Alts", $blob);
			$msg .= "Main: <highlight>$main<end> ($alts) ";
		} else if ($main != false) {
  			$alts = bot::makeLink("Alts of $main", $blob);
			$msg .= "$alts ";
		}

		$sql = "SELECT logon_msg FROM org_members_<myname> WHERE name = '{$sender}'";
		$db->query($sql);
		$row = $db->fObject();
        if ($row !== null && $row->logon_msg != '' ) {
            $msg .= " - " . $row->logon_msg;
		}
	}

	bot::send($msg, "guild", true);

	//private channel part
	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "priv", true);
	}
}

?>

<?php

if (isset($this->guildmembers[$sender]) && time() >= $this->vars["onlinedelay"]) {
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
			$alts = Text::make_link("Alts", $blob);
			$msg .= " Main: <highlight>$main<end> ($alts)";
		} else if ($main != false) {
  			$alts = Text::make_link("Alts of $main", $blob);
			$msg .= " $alts";
		}

		$sql = "SELECT logon_msg FROM org_members_<myname> WHERE name = '{$sender}'";
		$db->query($sql);
		$row = $db->fObject();
        if ($row !== null && $row->logon_msg != '') {
            $msg .= " - " . $row->logon_msg;
		}
	}

	$chatBot->send($msg, "guild", true);

	//private channel part
	if ($this->settings["guest_relay"] == 1) {
		$chatBot->send($msg, "priv", true);
	}
}

?>

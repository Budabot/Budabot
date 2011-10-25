<?php

if (Setting::get("relaybot") != "Off" && isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
    $whois = Player::get_by_name($sender);
	
	$msg = '';
	if ($whois === null) {
		$msg = "$sender logged on.";
	} else {
		$msg = Player::get_info($whois);

        $msg .= " logged on.";

		$altInfo = Alts::get_alt_info($sender);
		if (count($altInfo->alts) > 0) {
			$msg .= " " . $altInfo->get_alts_blob(false, true);
		}

		$sql = "SELECT logon_msg FROM org_members_<myname> WHERE name = '{$sender}'";
		$db->query($sql);
		$row = $db->fObject();
        if ($row !== null && $row->logon_msg != '') {
            $msg .= " - " . $row->logon_msg;
		}
    }

	send_message_to_relay("grc <grey>[<myguild>] ".$msg);
}

?>

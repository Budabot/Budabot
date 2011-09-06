<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	if (Setting::get('first_and_last_alt_only') == 1) {
		// if at least one alt/main is still online, don't show logoff message
		$altInfo = Alts::get_alt_info($sender);
		if (count($altInfo->get_online_alts()) > 1) {
			return;
		}
	}

	$whois = Player::get_by_name($sender);

	$msg = '';
	if ($whois === null) {
		$msg = "$sender logged on.";
	} else {
		$msg = Player::get_info($whois);

        $msg .= " logged on.";

		$altInfo = Alts::get_alt_info($sender);
		if (count($altInfo->alts) > 0) {
			$msg .= " " . $altInfo->get_alts_blob();
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
	if (Setting::get("guest_relay") == 1) {
		$chatBot->send($msg, "priv", true);
	}
}

?>

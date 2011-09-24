<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	if (Setting::get('first_and_last_alt_only') == 1) {
		// if at least one alt/main is already online, don't show logon message
		$altInfo = Alts::get_alt_info($sender);
		if (count($altInfo->get_online_alts()) > 0) {
			return;
		}
	}

	$whois = Player::get_by_name($sender);

	$msg = '';
	if ($whois === null) {
		$msg = "$sender logged off.";
	} else {
		$msg = Player::get_info($whois);

        $msg .= " logged off.";

		$altInfo = Alts::get_alt_info($sender);
		if (count($altInfo->alts) > 0) {
			$msg .= " " . $altInfo->get_alts_blob();
		}

		$sql = "SELECT logoff_msg FROM org_members_<myname> WHERE name = '{$sender}'";
		$db->query($sql);
		$row = $db->fObject();
        if ($row !== null && $row->logoff_msg != '') {
            $msg .= " - " . $row->logoff_msg;
		}
	}

	$chatBot->send($msg, "guild", true);

	//private channel part
	if (Setting::get("guest_relay") == 1) {
		$chatBot->send($msg, "priv", true);
	}
}
?>

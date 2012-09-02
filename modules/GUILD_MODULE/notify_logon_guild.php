<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	if ($setting->get('first_and_last_alt_only') == 1) {
		// if at least one alt/main is still online, don't show logoff message
		$alts = Registry::getInstance('alts');
		$altInfo = $alts->get_alt_info($sender);
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

		$alts = Registry::getInstance('alts');
		$altInfo = $alts->get_alt_info($sender);
		if (count($altInfo->alts) > 0) {
			$msg .= " " . $altInfo->get_alts_blob(false, true);
		}
	}

	$logon_msg = Preferences::get($sender, 'logon_msg');
	if ($logon_msg !== false && $logon_msg != '') {
		$msg .= " - " . $logon_msg;
	}

	$chatBot->sendGuild($msg, true);

	//private channel part
	if ($setting->get("guest_relay") == 1) {
		$chatBot->sendPrivate($msg, true);
	}
}

?>

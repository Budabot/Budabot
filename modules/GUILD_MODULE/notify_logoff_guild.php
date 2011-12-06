<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	if (Setting::get('first_and_last_alt_only') == 1) {
		// if at least one alt/main is already online, don't show logoff message
		$altInfo = Alts::get_alt_info($sender);
		if (count($altInfo->get_online_alts()) > 0) {
			return;
		}
	}

	$msg = "$sender logged off.";
	$logoff_msg = Preferences::get($sender, 'logoff_msg');
	if ($logoff_msg !== false && $logoff_msg != '') {
		$msg .= " - " . $logoff_msg;
	}

	$chatBot->send($msg, "guild", true);

	//private channel part
	if (Setting::get("guest_relay") == 1) {
		$chatBot->send($msg, "priv", true);
	}
}
?>

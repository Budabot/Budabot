<?php

if ($setting->get("relaybot") != "Off" && isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
    $whois = Player::get_by_name($sender);
	$alts = Registry::getInstance('alts');

	$msg = '';
	if ($whois === null) {
		$msg = "$sender logged on.";
	} else {
		$msg = Player::get_info($whois);

        $msg .= " logged on.";

		$altInfo = $alts->get_alt_info($sender);
		if (count($altInfo->alts) > 0) {
			$msg .= " " . $altInfo->get_alts_blob(false, true);
		}

		$logon_msg = Preferences::get($sender, 'logon_msg');
		if ($logon_msg !== false && $logon_msg != '') {
			$msg .= " - " . $logon_msg;
		}
    }

	send_message_to_relay("grc [<myguild>] ".$msg);
}

?>

<?php

if ($setting->get('relaybot') != 'Off' && $type == "joinpriv") {
	$whois = Player::get_by_name($sender);
	$alts = Registry::getInstance('alts');
	$altInfo = $alts->get_alt_info($sender);

	if ($whois !== null) {
		if (count($altInfo->alts) > 0) {
			$msg = Player::get_info($whois) . " has joined the private channel. " . $altInfo->get_alts_blob(false, true);
		} else {
			$msg = Player::get_info($whois) . " has joined the private channel.";
		}
	} else {
		if (count($altInfo->alts) > 0) {
			$msg .= "$sender has joined the private channel. " . $altInfo->get_alts_blob(false, true);
		} else {
			$msg = "$sender has joined the private channel.";
		}
	}

	send_message_to_relay("grc [<myguild>] ".$msg);
}

?>

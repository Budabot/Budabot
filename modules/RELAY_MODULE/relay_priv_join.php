<?php

if ($chatBot->settings['relaybot'] != 'Off' && $type == "joinPriv") {
	$whois = Player::get_by_name($sender);
	
	$altInfo = Alts::get_alt_info($sender);
	
	if ($whois !== null) {
		if (count($altInfo->alts) > 0) {
			$msg = Player::get_info($whois) . " has joined the private channel. " . $altInfo->get_alts_blob();
		} else {
			$msg = Player::get_info($whois) . " has joined the private channel.";
		}
	} else {
		if (count($altInfo->alts) > 0) {
			$msg .= "$sender has joined the private channel. " . $altInfo->get_alts_blob();
		} else {
			$msg = "$sender has joined the private channel.";
		}
	}

	send_message_to_relay("grc <grey>[<myguild>] ".$msg);
}

?>
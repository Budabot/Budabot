<?php

if ($type == "joinPriv") {
	$whois = Player::get_by_name($sender);
	
	$alts = Alts::get_alts_blob($sender);
	
	if ($whois !== null) {
		if ($alts !== null) {
			$msg = Player::get_info($whois) . " ($alts) has joined the private channel";
		} else {
			$msg = Player::get_info($whois) . " has joined the private channel";
		}
	} else {
		if ($alts !== null) {
			$msg .= "$sender ($alts) has joined the private channel";
		} else {
			$msg = "$sender has joined the private channel";
		}
	}

	if ($chatBot->settings["guest_relay"] == 1) {
		$chatBot->send($msg, "guild", true);
	}
	$chatBot->send($msg, "priv", true);
}

?>
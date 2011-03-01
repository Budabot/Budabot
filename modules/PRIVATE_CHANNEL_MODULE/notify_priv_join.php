<?php

if ($type == "joinPriv") {
	$whois = Player::get_by_name($sender);
	
	if ($whois !== null) {
		$msg = Player::get_info($whois) . " has joined the private channel";
	} else {
		$msg = "$sender has joined the private channel";
	}

	if ($chatBot->settings["guest_relay"] == 1) {
		$chatBot->send($msg, "guild", true);
	}
	$chatBot->send($msg, "priv", true);
}

?>
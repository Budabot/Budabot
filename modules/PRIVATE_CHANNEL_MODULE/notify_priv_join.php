<?php

if ($type == "joinPriv") {
	$whois = Player::get_by_name($sender);
	
	if ($whois !== null) {
		$msg = Player::get_info($whois) . " has joined the private channel";
	} else {
		$msg = "$sender has joined the private channel";
	}

	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "guild", true);
	}
	bot::send($msg, "priv", true);
}

?>
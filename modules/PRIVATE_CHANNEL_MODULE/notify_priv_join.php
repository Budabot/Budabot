<?php

if ($type == "joinPriv") {
	$whois = Player::get_by_name($sender);
	
	if ($whois !== null) {
		$msg = "<highlight>{$sender}<end> (<highlight>{$whois->level}<end>/<green>{$whois->ai_level}<end>, <highlight>{$whois->profession}<end>, {$whois->faction}) has joined the private channel";
	} else {
		$msg = "$sender has joined the private channel";
	}

	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "guild", true);
	}
	bot::send($msg, "priv", true);
}

?>
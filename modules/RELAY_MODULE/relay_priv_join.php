<?php

if ($this->settings['relaybot'] != 'Off' && $type == "joinPriv") {
	$whois = Player::get_by_name($sender);

	if ($whois === null) {
		$msg = "$sender has joined the private channel.";
	} else {
		$msg = Player::get_info($whois);

        $msg .= " has joined the private channel.";
	}
	send_message_to_relay("grc <grey>[".$this->vars["my guild"]."] ".$msg);
}

?>
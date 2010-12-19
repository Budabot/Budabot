<?php

if ($type == "joinPriv" && $this->settings['relaybot'] != 'Off') {
	$whois = Player::get_by_name($sender);
	$msg = "<highlight>{$sender}<end> (<highlight>{$whois->level}<end>/<green>{$whois->ai_level}<end>, <highlight>{$whois->prof}<end>, {$whois->faction}) has joined the private channel";
	send_message_to_relay("grc <grey>[".$this->vars["my guild"]."] ".$msg);
}

?>
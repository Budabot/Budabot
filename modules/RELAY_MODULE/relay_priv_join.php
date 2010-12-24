<?php

if ($type == "joinPriv") {
	$whois = Player::get_by_name($sender);
	$msg = "<highlight>{$sender}<end> (<highlight>{$whois->level}<end>/<green>{$whois->ai_level}<end>, <highlight>{$whois->profession}<end>, {$whois->faction}) has joined the private channel";
	send_message_to_relay("grc <grey>[".$this->vars["my guild"]."] ".$msg);
}

?>
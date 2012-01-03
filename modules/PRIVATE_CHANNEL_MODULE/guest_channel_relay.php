<?php

// Check if the private channel relay is enabled
if ($setting->get("guest_relay") != 1) {
	return;
}

// Check that it's not a command or if it is a command, check that guest_relay_commands is not disabled
if ($args[2][0] == $setting->get("symbol") && $setting->get("guest_relay_commands") != 1) {
	return;
}

$guest_color_channel = $setting->get("guest_color_channel");
$guest_color_guest = $setting->get("guest_color_guest");
$guest_color_guild = $setting->get("guest_color_guild");

if ($type == "priv") {
	//Relay the message to the guild channel
	$msg = "<end>{$guest_color_channel}[Guest]<end> ".Text::make_userlink($sender).": {$guest_color_guild}{$message}<end>";
	$chatBot->send($msg, 'org', true);
} else if ($type == "guild" && count($chatBot->chatlist) > 0) {
	//Relay the message to the private channel if there is at least 1 char in private channel
	if (!Util::isValidSender($sender)) {
		// for relaying city alien raid messages where $sender == -1
		$msg = "<end>{$guest_color_channel}[<myguild>]<end> {$guest_color_guest}{$message}<end>";
	} else {
		$msg = "<end>{$guest_color_channel}[<myguild>]<end> ".Text::make_userlink($sender).": {$guest_color_guest}{$message}<end>";
	}
	$chatBot->send($msg, 'prv', true);
}

?>

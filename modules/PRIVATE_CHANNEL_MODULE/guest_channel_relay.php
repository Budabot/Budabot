<?php

// Check if the private channel relay is enabled
if ($this->settings["guest_relay"] != 1) {
	return;
}

// Check that it's not a command or if it is a command, check that guest_relay_commands is not disabled
if ($args[2][0] == $this->settings["symbol"] && $this->settings["guest_relay_commands"] != 1) {
	return;
}

if ($type == "priv") {
	//Relay the message to the guild channel
	$msg = "<end>{$this->settings["guest_color_channel"]}[Guest]<end> {$this->settings["guest_color_username"]}".Text::make_link($sender,$sender,"user")."<end>: {$this->settings["guest_color_guild"]}{$message}<end>";
	bot::send($msg, 'org', true);
} else if ($type == "guild" && count($this->chatlist) > 0) {
	//Relay the message to the private channel if there is at least 1 char in private channel
	if ($sender == '-1') {
		// for relaying city alien raid messages where $sender == -1
		$msg = "<end>{$this->settings["guest_color_channel"]}[{$this -> vars["my guild"]}]<end> {$this->settings["guest_color_guest"]}{$message}<end>";
	} else {
		$msg = "<end>{$this->settings["guest_color_channel"]}[{$this -> vars["my guild"]}]<end> {$this->settings["guest_color_username"]}".Text::make_link($sender,$sender,"user")."<end>: {$this->settings["guest_color_guest"]}{$message}<end>";
	}
	bot::send($msg, 'prv', true);
}

?>

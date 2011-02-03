<?php

if ($this->settings["relaybot"] != "Off" && isset($this->guildmembers[$sender]) && time() >= $this->vars["onlinedelay"]) {
	send_message_to_relay("grc <grey>[".$this->vars["my guild"]."] <highlight>{$sender}<end> logged off");
}

?>

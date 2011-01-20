<?php

if (isset($this->guildmembers[$sender]) && time() >= $this->vars["onlinedelay"]) {
	$msg = "<highlight>$sender<end> logged off";
	
	bot::send($msg, "guild", true);

	//private channel part
	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "priv", true);
	}
}
?>

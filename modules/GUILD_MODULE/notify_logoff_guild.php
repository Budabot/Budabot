<?php

if (isset($this->guildmembers[$sender]) && time() >= $this->vars["logondelay"]) {
	$msg = "<highlight>$sender<end> logged off";

	$chatBot->send($msg, "guild", true);

	//private channel part
	if ($this->settings["guest_relay"] == 1) {
		$chatBot->send($msg, "priv", true);
	}
}
?>

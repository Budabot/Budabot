<?php

if (isset($chatBot->guildmembers[$sender]) && time() >= $chatBot->vars["logondelay"]) {
	$msg = "<highlight>$sender<end> logged off";

	$chatBot->send($msg, "guild", true);

	//private channel part
	if ($chatBot->settings["guest_relay"] == 1) {
		$chatBot->send($msg, "priv", true);
	}
}

?>

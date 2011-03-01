<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	$msg = "<highlight>$sender<end> logged off";

	$chatBot->send($msg, "guild", true);

	//private channel part
	if ($chatBot->settings["guest_relay"] == 1) {
		$chatBot->send($msg, "priv", true);
	}
}

?>

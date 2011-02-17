<?php

if ($type == "msg" && isset($chatBot->vars["broadcast_list"][$sender])) {
	$msg = "[$sender]: $message";

	if ($chatBot->settings['broadcast_to_guild']) {
		$chatBot->send($msg, 'guild', true);
	}
	if ($chatBot->settings['broadcast_to_privchan']) {
		$chatBot->send($msg, 'priv', true);
	}

	// keeps the bot from sending a message back to the neutnet satellite bot
	$stop_execution = true;
}

?>
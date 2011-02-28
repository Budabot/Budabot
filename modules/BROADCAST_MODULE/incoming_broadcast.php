<?php

if ($type == "msg" && isset($chatBot->data["broadcast_list"][$charid])) {
	$msg = "[$sender]: $message";

	if (Setting::get('broadcast_to_guild')) {
		$chatBot->send($msg, 'guild', true);
	}
	if (Setting::get('broadcast_to_privchan')) {
		$chatBot->send($msg, 'priv', true);
	}

	// keeps the bot from sending a message back to the neutnet satellite bot
	$stop_execution = true;
}

?>
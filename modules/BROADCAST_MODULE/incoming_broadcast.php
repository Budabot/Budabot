<?php

if (isset($chatBot->data["broadcast_list"][$sender])) {
	$msg = "[$sender]: $message";

	if ($setting->get('broadcast_to_guild')) {
		$chatBot->sendGuild($msg, true);
	}
	if ($setting->get('broadcast_to_privchan')) {
		$chatBot->sendPrivate($msg, true);
	}

	// keeps the bot from sending a message back to the neutnet satellite bot
	$stop_execution = true;
}

?>
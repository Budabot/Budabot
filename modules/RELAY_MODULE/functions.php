<?php

function send_message_to_relay($message) {
	global $chatBot;
	
	// we use the aochat methods so the bot doesn't prepend default colors
	
	// since we are using the aochat methods, we have to call formatMessage manually to handle colors and bot name replacement
	$message = $chatBot->formatMessage($message);

	if ($chatBot->settings['relaytype'] == 2) {
		$chatBot->send_privgroup($chatBot->settings['relaybot'], $message);
	} else if ($chatBot->settings['relaytype'] == 1) {
		$chatBot->send_tell($chatBot->settings["relaybot"], $message);
	}
}

?>
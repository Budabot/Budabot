<?php

function send_message_to_relay($message) {
	global $chatBot;
	
	// we use the aochat methods so the bot doesn't prepend default colors
	
	// since we are using the aochat methods, we have to call formatMessage manually to handle colors and bot name replacement
	$message = $chatBot->formatMessage($message);
	$relayBot = $chatBot->settings['relaybot'];

	if ($chatBot->settings['relaytype'] == 2) {
		$chatBot->send_privgroup($relayBot, $message);
	} else if ($chatBot->settings['relaytype'] == 1) {
		$chatBot->send_tell($relayBot, $message);
		
		// manual logging is only needed for tell relay
		Logger::log_chat('chat', "Out. Msg.", $relayBot, $message);
	}
}

?>
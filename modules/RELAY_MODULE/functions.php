<?php

function send_message_to_relay($message) {
	global $chatBot;

	if ($chatBot->settings[''] == 2) {
		$chatBot->send_privgroup($chatBot->settings['relaybot'], "$sender: " . substr($message, 1));
	} else if ($chatBot->settings[''] == 1) {
		$chatBot->send($message, $chatBot->settings["relaybot"]);
	}
}

?>
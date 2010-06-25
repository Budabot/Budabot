<?php

function send_message_to_relay($message) {
	global $chatBot;

	if ($chatBot->settings['relaytype'] == 2) {
		$chatBot->send_privgroup($chatBot->settings['relaybot'], $message);
	} else if ($chatBot->settings['relaytype'] == 1) {
		$chatBot->send($message, $chatBot->settings["relaybot"]);
	}
}

?>
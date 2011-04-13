<?php

if (($chatBot->settings["relaybot"] != "Off") && ($chatBot->settings["bot_relay_commands"] == 1 || $args[2][0] != $chatBot->settings["symbol"])) {
	$relayMessage = '';
	if ($chatBot->settings['relaysymbol'] == 'Always relay') {
		$relayMessage = $message;
	} else if ($args[2][0] == $chatBot->settings['relaysymbol']) {
		$relayMessage = substr($args[2], 1);
	}

	if ($relayMessage != '') {
		if ($sender == -1) {
			$sender_link = '';
		} else {
			$sender_link = ' ' . Text::make_link($sender, $sender, "user") . ':';
		}

		if ($type == "guild") {
			$msg = "grc [<myguild>]{$sender_link} {$relayMessage}";
		} else if ($type == "priv") {
			$msg = "grc [<myguild>] [Guest]{$sender_link} {$relayMessage}";
		}
        send_message_to_relay($msg);
	}
}

?>
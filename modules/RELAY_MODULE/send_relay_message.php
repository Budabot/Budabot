<?php

if ((Setting::get("relaybot") != "Off") && (Setting::get("bot_relay_commands") == 1 || $args[2][0] != Setting::get("symbol"))) {
	$relayMessage = '';
	if (Setting::get('relaysymbol') == 'Always relay') {
		$relayMessage = $message;
	} else if ($args[2][0] == Setting::get('relaysymbol')) {
		$relayMessage = substr($args[2], 1);
	}

	if ($relayMessage != '') {
		if ($sender == -1) {
			$sender_link = '';
		} else {
			$sender_link = ' ' . Text::make_userlink($sender) . ':';
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
<?php

if ((Setting::get("relaybot") != "Off") && (Setting::get("bot_relay_commands") == 1 || $args[2][0] != Setting::get("symbol"))) {
	$relayMessage = '';
	if (Setting::get('relaysymbol') == 'Always relay') {
		$relayMessage = $message;
	} else if ($args[2][0] == Setting::get('relaysymbol')) {
		$relayMessage = substr($args[2], 1);
	}

	if ($relayMessage != '') {
		$sender_link = Text::make_link($sender, $sender, "user");
		$guild = $chatBot->vars["my guild"];
		if ($type == "guild") {
			$msg = "grc <grey>[{$guild}] {$sender_link}: ".$relayMessage."</font>";
		} else if ($type == "priv") {
			$msg = "grc <grey>[{$guild}] [Guest] {$sender_link}: ".$relayMessage."</font>";
		}
        send_message_to_relay($msg);
	}
}

?>
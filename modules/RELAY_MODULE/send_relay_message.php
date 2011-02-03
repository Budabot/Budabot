<?php

if (($this->settings["relaybot"] != "Off") && ($this->settings["bot_relay_commands"] == 1 || $args[2][0] != $this->settings["symbol"])) {
	$relayMessage = '';
	if ($this->settings['relaysymbol'] == 'Always relay') {
		$relayMessage = $message;
	} else if ($args[2][0] == $this->settings['relaysymbol']) {
		$relayMessage = substr($args[2], 1);
	}

	if ($relayMessage != '') {
		$sender_link = bot::makeLink($sender, $sender, "user");
		$guild = $this->vars["my guild"];
		if ($type == "guild") {
			$msg = "grc <grey>[{$guild}] {$sender_link}: ".$relayMessage."</font>";
		} else if ($type == "priv") {
			$msg = "grc <grey>[{$guild}] [Guest] {$sender_link}: ".$relayMessage."</font>";
		}
        send_message_to_relay($msg);
	}
}

?>
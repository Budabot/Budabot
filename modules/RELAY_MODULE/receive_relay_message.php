<?php

if (($sender == $this->settings['relaybot'] || $channel == $this->settings['relaybot']) && preg_match("/^grc (.+)$/", $message, $arr)) {
	$msg = $arr[1];
    bot::send($msg, "guild", true);

	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "priv", true);
	}
}

?>
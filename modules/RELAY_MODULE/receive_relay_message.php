<?php

if (($sender == ucfirst(strtolower($this->settings['relaybot'])) || $channel == ucfirst(strtolower($this->settings['relaybot']))) && preg_match("/^grc (.+)$/", $message, $arr)) {
	$msg = $arr[1];
    $this->send($msg, "guild", true);

	if ($this->settings["guest_relay"] == 1) {
		$this->send($msg, "priv", true);
	}
}

?>
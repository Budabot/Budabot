<?php

if ($type == "leavePriv") {
	$msg = "$sender has left the private channel";

	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "guild", true);
	}
	bot::send($msg, "priv", true);
}

?>
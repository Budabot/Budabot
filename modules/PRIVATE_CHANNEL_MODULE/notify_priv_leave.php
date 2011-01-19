<?php

if ($type == "leavePriv") {
	$msg = "$sender has left the private channel";

	if ($this->settings["guest_relay"] == 1) {
		bot::send($msg, "guild", true);
	}
	
	// don't need this since the client tells you when someone leaves and we don't add any additional information
	//bot::send($msg, "priv", true);
}

?>
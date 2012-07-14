<?php

if ($type == "leavepriv") {
	$msg = "$sender has left the private channel";

	if ($setting->get("guest_relay") == 1) {
		$chatBot->sendGuild($msg, true);
	}

	// don't need this since the client tells you when someone leaves and we don't add any additional information
	//$chatBot->sendPrivate($msg, true);
}

?>

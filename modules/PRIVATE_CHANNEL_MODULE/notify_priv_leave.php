<?php

if ($type == "leavePriv") {
	$msg = "$sender has left the private channel";

	if (Setting::get("guest_relay") == 1) {
		$chatBot->send($msg, "guild", true);
	}
	
	// don't need this since the client tells you when someone leaves and we don't add any additional information
	//$chatBot->send($msg, "priv", true);
}

?>
<?php

global $bbinSocket;
if (preg_match("/^stopbbin$/i", $message)) {
	Setting::save("bbin_status", "0");

	if (!IRC::isConnectionActive($bbinSocket)) {
		$chatBot->send("There is no active BBIN connection.", $sendto);
	} else {
		IRC::disconnect($bbinSocket);
		Logger::log('INFO', "BBIN", "Disconnected from BBIN");
		$chatBot->send("The BBIN connection has been disconnected.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
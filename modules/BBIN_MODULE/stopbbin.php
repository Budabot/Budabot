<?php

global $bbinSocket;
if (preg_match("/^stopbbin$/i", $message)) {
	$setting->save("bbin_status", "0");

	if (!IRC::isConnectionActive($bbinSocket)) {
		$sendto->reply("There is no active BBIN connection.");
	} else {
		IRC::disconnect($bbinSocket);
		LegacyLogger::log('INFO', "BBIN", "Disconnected from BBIN");
		$sendto->reply("The BBIN connection has been disconnected.");
	}
} else {
	$syntax_error = true;
}

?>
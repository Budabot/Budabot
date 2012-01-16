<?php

global $ircSocket;
if (preg_match("/^stopirc$/i", $message)) {
	$setting->save("irc_status", "0");

	if (!IRC::isConnectionActive($ircSocket)) {
		$sendto->reply("There is no active IRC connection.");
	} else {
		IRC::disconnect($ircSocket);
		LegacyLogger::log('INFO', "IRC", "Disconnected from IRC");
		$sendto->reply("The IRC connection has been disconnected.");
	}
} else {
	$syntax_error = true;
}

?>

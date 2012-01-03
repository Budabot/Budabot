<?php

global $ircSocket;
if (preg_match("/^stopirc$/i", $message)) {
	$setting->save("irc_status", "0");

	if (!IRC::isConnectionActive($ircSocket)) {
		$chatBot->send("There is no active IRC connection.", $sendto);
	} else {
		IRC::disconnect($ircSocket);
		Logger::log('INFO', "IRC", "Disconnected from IRC");
		$chatBot->send("The IRC connection has been disconnected.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>

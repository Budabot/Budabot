<?php

if (Setting::get('irc_status') != 1) {
	$chatBot->send("There is no active IRC connection.", $sendto);
} else {
	global $socket;
	fclose($socket);
	Logger::log('info', "IRC", "Disconnected from IRC");
	Setting::save("irc_status", "0");
	$chatBot->send("The IRC connection has been disconnected.", $sendto);
}

?>

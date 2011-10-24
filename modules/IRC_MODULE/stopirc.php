<?php

if (!IRC::isConnectionActive()) {
	$chatBot->send("There is no active IRC connection.", $sendto);
} else {
	IRC::disconnect();
	Logger::log('info', "IRC", "Disconnected from IRC");
	Setting::save("irc_status", "0");
	$chatBot->send("The IRC connection has been disconnected.", $sendto);
}

?>

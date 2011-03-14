<?php

if (Setting::get('bbin_status') != 1) {
	$chatBot->send("There is no active BBIN connection.", $sendto);
} else {
	global $socket;
	fclose($socket);
	Logger::log('info', "BBIN", "Disconnected from BBIN");
	Setting::save("bbin_status", "0");
	$chatBot->send("The BBIN connection has been disconnected.", $sendto);
}

?>
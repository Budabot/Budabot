<?php

global $bbinSocket;
if (preg_match("/^onlinebbin$/i", $message)) {
	if (!IRC::isConnectionActive($bbinSocket)) {
		$sendto->reply("There is no active IRC connection.");
		return;
	}

	$names = IRC::getUsersInChannel($bbinSocket, $setting->get('bbin_channel'));
	$numusers = count($names);
	$blob = '';
	forEach ($names as $value) {
		$blob .= "$value\n";
	}

	$msg = Text::make_blob("BBIN Online ($numusers)", $blob);

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>

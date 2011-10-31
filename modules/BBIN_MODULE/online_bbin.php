<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $bbinSocket;
if (preg_match("/^onlinebbin$/i", $message)) {
	if (!IRC::isConnectionActive($bbinSocket)) {
		$chatBot->send("There is no active IRC connection.", $sendto);
		return;
	}

	$names = IRC::getUsersInChannel($bbinSocket, Setting::get('bbin_channel'));
	$numusers = count($names);
	$blob = "<header> :::::: BBIN Online ($numusers) :::::: <end>\n\n";
	forEach ($names as $value) {
		$blob .= "$value\n";
	}
	
	$msg = Text::make_blob("BBIN Online ($numusers)", $blob);
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

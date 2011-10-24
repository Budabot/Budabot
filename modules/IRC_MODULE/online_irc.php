<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
if (preg_match("/^onlineirc$/i", $message)) {
	if (!IRC::isConnectionActive()) {
		$chatBot->send("There is no active IRC connection.", $sendto);
		return;
	}

	$names = IRC::getUsersInChannel();
	$numusers = count($names);
	$blob = "<header> :::::: IRC Online ($numusers) :::::: <end>\n\n";
	forEach ($names as $value) {
		$blob .= "$value\n";
	}
	
	$msg = Text::make_blob("IRC Online ($numusers)", $blob);
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

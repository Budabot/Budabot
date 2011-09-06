<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $socket;
stream_set_blocking($socket, 0);
if (preg_match("/^onlineirc$/i", $message)) {
	if (Setting::get('irc_status') != 1) {
		$chatBot->send("There is no active IRC connection.", $sendto);
		return;
	}

	fputs($socket, "NAMES :".Setting::get('irc_channel')."\n");
	sleep(1);
	while ($data = fgets($socket)) {
		if (preg_match("/(End of \/NAMES list)/", $data)) {
			break;
		} else {
			$start = strrpos($data,":")+1;
			$names = explode(' ',substr($data, $start, strlen($data)));
			$numusers = count($names);
			$blob = "<header> :::::: IRC Online List :::::: <end>\n\n";
			forEach ($names as $value) {
				$blob .= "$value\n";
			}
			
			$msg = Text::make_blob("$numusers online in IRC", $blob);
			
			$chatBot->send($msg, $sendto);
		}
		flush();
	}
} else {
	$syntax_error = true;
}

?>

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
set_time_limit(0);
	//settings
	if($chatBot->settings['irc_server'] == "") {
		$chatBot->send("The IRC <highlight>server address<end> seems to be missing. Please <highlight>/tell <myname> <symbol>help irc<end> for details on setting this");
		return;
	}
	if($chatBot->settings['irc_port'] == "") {
		$chatBot->send("The IRC <highlight>server port<end> seems to be missing. Please <highlight>/tell <myname> <symbol>help irc<end> for details on setting this");
		return;
	}
	
	$nick = $chatBot->settings['irc_nickname'];
	 
	// Connection
	if(preg_match("/^startirc$/i", $message)) {
		$chatBot->send("Intialized IRC connection. Please wait...",$sender);
	}
	Logger::log('info', "IRC", "Intialized IRC connection. Please wait...");
	$socket = fsockopen($chatBot->settings['irc_server'], $chatBot->settings['irc_port']);
	fputs($socket,"USER $nick $nick $nick $nick :$nick\n");
	fputs($socket,"NICK $nick\n");
	while($logincount < 10) {
		$logincount++;
		$data = fgets($socket, 128);
		if($chatBot->settings['irc_debug_all'] == 1)
		{
			Logger::log('info', "IRC", trim($data));
		}
		// Separate all data
		$ex = explode(' ', $data);

		// Send PONG back to the server
		if($ex[0] == "PING") {
			fputs($socket, "PONG ".$ex[1]."\n");
		}
		flush();
	}
	sleep(1);
	fputs($socket,"JOIN ".$chatBot->settings['irc_channel']."\n");
	
	while($data = fgets($socket)) {
		if($chatBot->settings['irc_debug_all'] == 1)
		{
			Logger::log('info', "IRC", trim($data));
		}
		if(preg_match("/(ERROR)(.+)/", $data, $sandbox)) {
			if(preg_match("/^startirc$/i", $message)) {
				$chatBot->send("[red]Could not connect to IRC",$sender);
			}
			Logger::log('error', "IRC", trim($data));
			return;
		}
		if($ex[0] == "PING") {
			fputs($socket, "PONG ".$ex[1]."\n");
		}
		if(preg_match("/(End of \/NAMES list)/", $data, $discard)) {
			break;
		}
		flush();
	}
	if(preg_match("/^startirc$/i", $message)) {
		$chatBot->send("Finished connecting to IRC",$sender);
	}
	Logger::log('info', "IRC", "Finished connecting to IRC");
	Setting::save("irc_status", "1");
?>
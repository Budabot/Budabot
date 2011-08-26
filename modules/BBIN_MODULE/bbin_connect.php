<?php
	 /*
   ** Author: Mindrila (RK1)
   ** Credits: Legendadv (RK2)
   ** BUDABOT IRC NETWORK MODULE
   ** Version = 0.1
   ** Developed for: Budabot(http://budabot.com)
   **
   */
   
global $bbin_socket;
$db = DB::get_instance();
require_once("bbin_func.php");

stream_set_blocking($bbin_socket, 0);
set_time_limit(0);
$nick = $chatBot->settings['bbin_nickname'];
 
// Connection
if (preg_match("/^startbbin$/i", $message)) {
	$chatBot->send("Intialized BBIN connection. Please wait...",$sender);
}

Logger::log('info', "BBIN", "Intialized BBIN connection. Please wait...");
$bbin_socket = fsockopen($chatBot->settings['bbin_server'], $chatBot->settings['bbin_port']);
fputs($bbin_socket,"USER $nick $nick $nick $nick :$nick\n");
fputs($bbin_socket,"NICK $nick\n");
while ($logincount < 10) {
	$logincount++;
	$data = fgets($bbin_socket, 128);
	if ($chatBot->settings['bbin_debug_all'] == 1) {
		Logger::log('debug', "BBIN", trim($data));
	}
	// Separate all data
	$ex = explode(' ', $data);

	// Send PONG back to the server
	if ($ex[0] == "PING") {
		fputs($bbin_socket, "PONG ".$ex[1]."\n");
	}
	flush();
}
sleep(1);
fputs($bbin_socket,"JOIN ".$chatBot->settings['bbin_channel']."\n");

while ($data = fgets($bbin_socket)) {
	if ($chatBot->settings['bbin_debug_all'] == 1) {
		Logger::log('DEBUG', "BBIN", trim($data));
	}
	if (preg_match("/(ERROR)(.+)/", $data, $sandbox)) {
		Logger::log('ERROR', "BBIN", trim($data));
		if (preg_match("/^startbbin$/i", $message)) {
			$chatBot->send("[red]Could not connect to BBIN",$sender);
		}
		return;
	}
	if ($ex[0] == "PING") {
		fputs($bbin_socket, "PONG ".$ex[1]."\n");
	}
	if (preg_match("/(End of \/NAMES list)/", $data, $discard)) {
		break;
	}
	flush();
}

// send a synchronize request to network
fputs($bbin_socket, "PRIVMSG ".$chatBot->settings['bbin_channel']." :[BBIN:SYNCHRONIZE]\n");

// call the synchronize function ourselves, to send our online list to the network
parse_incoming_bbin("[BBIN:SYNCHRONIZE]", $nick);

if (preg_match("/^startbbin$/i", $message)) {
	$chatBot->send("Finished connecting to bbin",$sender);
}
Logger::log('INFO', "BBIN", "Finished connecting to bbin");
Setting::save("bbin_status", "1");

?>

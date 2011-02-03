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
$db = db::get_instance();
require_once("bbin_func.php");

stream_set_blocking($bbin_socket, 0);
set_time_limit(0);
$nick = $this->settings['bbin_nickname'];
 
// Connection
if(preg_match("/^startbbin$/i", $message)) {
	bot::send("Intialized BBIN connection. Please wait...",$sender);
}

Logger::log('info', "BBIN", "Intialized BBIN connection. Please wait...");
$bbin_socket = fsockopen($this->settings['bbin_server'], $this->settings['bbin_port']);
fputs($bbin_socket,"USER $nick $nick $nick $nick :$nick\n");
fputs($bbin_socket,"NICK $nick\n");
while($logincount < 10) {
	$logincount++;
	$data = fgets($bbin_socket, 128);
	if($this->settings['bbin_debug_all'] == 1)
	{
		Logger::log('debug', "BBIN", trim($data));
	}
	// Separate all data
	$ex = explode(' ', $data);

	// Send PONG back to the server
	if($ex[0] == "PING"){
	fputs($bbin_socket, "PONG ".$ex[1]."\n");
	}
	flush();
}
sleep(1);
fputs($bbin_socket,"JOIN ".$this->settings['bbin_channel']."\n");

while($data = fgets($bbin_socket)) {
	if($this->settings['bbin_debug_all'] == 1)
	{
		Logger::log('debug', "BBIN", trim($data));
	}
	if(preg_match("/(ERROR)(.+)/", $data, $sandbox)) {
		Logger::log('error', "BBIN", trim($data));
		if(preg_match("/^startbbin$/i", $message)) {
			bot::send("[red]Could not connect to BBIN",$sender);
		}
		return;
	}
	if($ex[0] == "PING") {
		fputs($bbin_socket, "PONG ".$ex[1]."\n");
	}
	if(preg_match("/(End of \/NAMES list)/", $data, $discard)) {
		break;
	}
	flush();
}

// send a synchronize request to network
fputs($bbin_socket, "PRIVMSG ".$this->settings['bbin_channel']." :[BBIN:SYNCHRONIZE]\n");

// call the synchronize function ourselves, to send our online list to the network
parse_incoming_bbin("[BBIN:SYNCHRONIZE]", $nick, $this);

if(preg_match("/^startbbin$/i", $message)) {
	bot::send("Finished connecting to bbin",$sender);
}
Logger::log('info', "BBIN", "Finished connecting to bbin");

Setting::save("bbin_status", "1");
?>

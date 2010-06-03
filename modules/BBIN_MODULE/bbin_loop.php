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
global $db;
require_once("bbin_func.php");

stream_set_blocking($bbin_socket, 0);
if(($data = fgets($bbin_socket)) && ("1" == $this->settings['bbin_status'])) {
	$ex = explode(' ', $data);
	$ex[3] = substr($ex[3],1,strlen($ex[3]));
	if($this->settings['bbin_debug_messages'] == 1)
	{
		echo $data."\n";
	}
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);

	$host = $nicka[1];
	$nick = $nickc[1];
	if($ex[0] == "PING"){
		fputs($bbin_socket, "PONG ".$ex[1]."\n");
		if($this->settings['bbin_debug_ping'] == 1) {
			echo("[".date('H:i')."] [bbin] PING received. PONG sent.\n");
		}
	}
	elseif(($ex[1] == "QUIT") || ($ex[1] == "PART")) {
		$db->query("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$nick'");
		if($this->vars['my guild'] != "") {
			bot::send("<yellow>[BBIN]<end> Lost uplink with $nick","guild",true);
		}
		if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1) {
			bot::send("<yellow>[BBIN]<end> Lost uplink with $nick","priv",true);
		}
	}
	elseif($ex[1] == "JOIN") {
	        if($this->vars['my guild'] != "") {
        	        bot::send("<yellow>[BBIN]<end> Uplink established with $nick.","guild",true);
                }
                if($this->vars['my guild'] == "" || $this->settings["guest_relay"] == 1) {
                	bot::send("<yellow>[BBIN]<end> Uplink established with $nick.","priv",true);
                }
        }
	elseif($channel == trim(strtolower($this->settings['bbin_channel']))) {
		for ($i = 3; $i < count($ex); $i++) {
			$bbinmessage .= rtrim(htmlspecialchars_decode($ex[$i]))." ";
		}
		if($this->settings['bbin_debug_messages'] == 1) {
			echo("[".date('H:i')."] [Inc. IRC Msg.] $nick: $bbinmessage\n");
		}
		parse_incoming_bbin($bbinmessage, $nick, $this);
		
		flush();
	}
	unset($sandbox);
}
?>

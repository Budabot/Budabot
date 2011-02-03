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
if(($data = fgets($bbin_socket)) && ("1" == $this->settings['bbin_status'])) {
	$ex = explode(' ', $data);
	if($this->settings['bbin_debug_all'] == 1)
	{
		Logger::log('debug', "BBIN", trim($data));
	}
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);

	$host = $nicka[1];
	$nick = $nickc[1];
	if($ex[0] == "PING")
	{
		fputs($bbin_socket, "PONG ".$ex[1]."\n");
		if($this->settings['bbin_debug_ping'] == 1)
		{
			Logger::log('debug', "BBIN", "PING received. PONG sent.");
		}
	}
	elseif ($ex[1] == "NOTICE")
	{
		if ( false != stripos($data, "exiting"))
		{
			// the irc server shut down (i guess)
			// set bot to disconnected
			Setting::save("bbin_status","0");


			// send notification to channel
			$extendedinfo = bot::makelink("Extended informations",$data);
			if($this->vars['my guild'] != "")
			{
				bot::send("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo,"guild",true);
			}
			if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1)
			{
				bot::send("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo,"priv",true);
			}
		}
	}
	elseif ("KICK" == $ex[1])
	{
		$extendedinfo = bot::makelink("Extended informations",$data);
		if ($ex[3] == $this->settings['bbin_nickname'])
		{
			// oh noez, I was kicked !
			Setting::save("bbin_status", "0");
			if($this->vars['my guild'] != "")
			{
				bot::send("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo,"guild",true);
			}
			if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1)
			{
				bot::send("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo,"priv",true);
			}
		}
		else
		{
			// yay someone else was kicked
			$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$ex[3]'");
			if($this->vars['my guild'] != "")
			{
				bot::send("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo,"guild",true);
			}
			if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1)
			{
				bot::send("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo,"priv",true);
			}
		}
	}
	elseif(($ex[1] == "QUIT") || ($ex[1] == "PART"))
	{
		$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$nick'");
		if($this->vars['my guild'] != "")
		{
			bot::send("<yellow>[BBIN]<end> Lost uplink with $nick","guild",true);
		}
		if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1)
		{
			bot::send("<yellow>[BBIN]<end> Lost uplink with $nick","priv",true);
		}
	}
	elseif($ex[1] == "JOIN")
	{
		if($this->vars['my guild'] != "")
		{
			bot::send("<yellow>[BBIN]<end> Uplink established with $nick.","guild",true);
		}
		if($this->vars['my guild'] == "" || $this->settings["guest_relay"] == 1)
		{
			bot::send("<yellow>[BBIN]<end> Uplink established with $nick.","priv",true);
		}
	}
	elseif($channel == trim(strtolower($this->settings['bbin_channel'])))
	{
		// tweak the third message a bit to remove beginning ":"
		$ex[3] = substr($ex[3],1,strlen($ex[3]));
		for ($i = 3; $i < count($ex); $i++)
		{
			$bbinmessage .= rtrim(htmlspecialchars_decode($ex[$i]))." ";
		}
		if($this->settings['bbin_debug_messages'] == 1)
		{
			Logger::log_chat("Inc. IRC Msg.", $nick, $bbinmessage);
		}
		parse_incoming_bbin($bbinmessage, $nick, $this);

		flush();
	}
	unset($sandbox);
}
?>

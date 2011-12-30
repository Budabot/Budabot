<?php

   /*
   ** Author: Mindrila (RK1)
   ** Credits: Legendadv (RK2)
   ** BUDABOT IRC NETWORK MODULE
   ** Developed for: Budabot(http://budabot.com)
   **
   */
   
	require_once "bbin_func.php";

	$channel = Setting::get('bbin_channel');
	if ($channel === false) {
		if ($chatBot->vars['my_guild'] == "") {
			$channel = "#".strtolower($chatBot->vars['name']);
		} else {
			if (strpos($chatBot->vars['my_guild']," ")) {
				$sandbox = explode(" ",$chatBot->vars['my_guild']);
				for ($i = 0; $i < count($sandbox); $i++) {
					$channel .= ucfirst(strtolower($sandbox[$i]));
				}
				$channel = "#".$channel;
			} else {
				$channel = "#".$chatBot->vars['my_guild'];
			}
		}
	}

	// Setup
	$db->loadSQLFile($MODULE_NAME, "bbin_chatlist");
	
	$event->register($MODULE_NAME, "1min", "set_bbin_link.php", "Automatically reconnect to IRC server", '', 0);
	
	//Commands
	$command->register($MODULE_NAME, "", "bbin_connect.php", "startbbin", "mod", "Connect to BBIN", 'bbin');
	$command->register($MODULE_NAME, "", "stopbbin.php", "stopbbin", "mod", "Disconnect from BBIN", 'bbin');
	$command->register($MODULE_NAME, "", "online_bbin.php", "onlinebbin", "all", "View who is in IRC channel", 'bbin');
	$command->register($MODULE_NAME, "", "set_bbin_settings.php", "setbbin", "mod", "Manually set BBIN settings", 'bbin');
	
	//BBIN Relay
	$event->register($MODULE_NAME, "2sec", "bbin_loop.php", "The main BBIN message loop");
	
	//In-game relay
	$event->register($MODULE_NAME, "priv", "relay_bbin_out.php", "Relay (priv) messages to BBIN");
	$event->register($MODULE_NAME, "guild", "relay_bbin_out.php", "Relay (guild) messages to BBIN");
	
	//Notifications
	$event->register($MODULE_NAME, "joinPriv", "bbin_relay_joined.php", "Sends joined channel messages");
	$event->register($MODULE_NAME, "leavePriv", "bbin_relay_left.php", "Sends left channel messages");
	$event->register($MODULE_NAME, "logOn", "bbin_relay_joined.php", "Shows a logon from a member");
	$event->register($MODULE_NAME, "logOff", "bbin_relay_left.php", "Shows a logoff from a member");
	
	//Settings
	Setting::add($MODULE_NAME, "bbin_status", "Status of BBIN uplink", "noedit", "options", "0", "Offline;Online", "0;1", "mod", "bbin");
	Setting::add($MODULE_NAME, "bbin_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "", "", "mod", "bbin");
	Setting::add($MODULE_NAME, "bbin_port", "IRC server port to use", "noedit", "number", "6667", "", "", "mod", "bbin");
	Setting::add($MODULE_NAME, "bbin_nickname", "Nickname to use while in IRC", "noedit", "text", $chatBot->vars['name'], "", "", "mod", "bbin");
	Setting::add($MODULE_NAME, "bbin_channel", "Channel to join", "noedit", "text", $channel, "", "", "mod", "bbin");
	Setting::add($MODULE_NAME, "bbin_password", "IRC password to join channel", "edit", "text", "none", "none");
	
	//Helpfiles
	Help::register($MODULE_NAME, "bbin", "bbin_help.txt", "all", "How to use the BBIN plugin");
?>
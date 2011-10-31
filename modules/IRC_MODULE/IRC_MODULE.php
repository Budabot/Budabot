<?php

   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
	require_once 'functions.php';
	require_once 'IRC.class.php';

	$channel = Setting::get('irc_channel');
	if ($channel === false) {
		if ($chatBot->vars['my_guild'] == "") {
			$channel = "#".strtolower($chatBot->vars['name']);
		} else {
			if (strpos($chatBot->vars['my_guild']," ")) {
				$sandbox = explode(" ", $chatBot->vars['my_guild']);
				for ($i = 0; $i < count($sandbox); $i++) {
					$channel .= ucfirst(strtolower($sandbox[$i]));
				}
				$channel = "#".$channel;
			} else {
				$channel = "#".$chatBot->vars['my_guild'];
			}
		}
	}

	Event::register($MODULE_NAME, "1min", "set_irc_link.php", "none", "Automatically reconnect to IRC server", '', 0);
	
	// Commands
	Command::register($MODULE_NAME, "", "irc_connect.php", "startirc", "mod", "Connect to IRC", 'irc');
	Command::register($MODULE_NAME, "", "stopirc.php", "stopirc", "mod", "Disconnect from IRC", 'irc');
	Command::register($MODULE_NAME, "", "online_irc.php", "onlineirc", "all", "View who is in IRC chat", 'irc');
	Command::register($MODULE_NAME, "", "set_irc_settings.php", "setirc", "mod", "Manually set IRC settings", 'irc');
	
	// IRC Relay
  	Event::register($MODULE_NAME, "2sec", "irc_check.php", "none", "Receive messages from IRC");
	
	// In-game relay
	Event::register($MODULE_NAME, "priv", "relay_irc_out.php", "none", "Relay (priv) messages to IRC");
	Event::register($MODULE_NAME, "guild", "relay_irc_out.php", "none", "Relay (guild) messages to IRC");
	
	// Notifications
	Event::register($MODULE_NAME, "joinPriv", "irc_relay_joined.php", "none", "Sends joined channel messages");
	Event::register($MODULE_NAME, "leavePriv", "irc_relay_left.php", "none", "Sends left channel messages");
	Event::register($MODULE_NAME, "logOn", "irc_relay_joined.php", "none", "Shows a logon from a member");
	Event::register($MODULE_NAME, "logOff", "irc_relay_left.php", "none", "Shows a logoff from a member");
	
	// Settings
	Setting::add($MODULE_NAME, "irc_status", "Status of IRC uplink", "noedit", "options", "0", "Offline;Online", "0;1");
	Setting::add($MODULE_NAME, "irc_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "irc.funcom.com");
	Setting::add($MODULE_NAME, "irc_port", "IRC server port to use", "noedit", "number", "6667", "6667");
	Setting::add($MODULE_NAME, "irc_nickname", "Nickname to use while in IRC", "noedit", "text", $chatBot->vars['name'], $chatBot->vars['name']);
	Setting::add($MODULE_NAME, "irc_channel", "Channel to join", "noedit", "text", $channel, $channel);
	Setting::add($MODULE_NAME, "irc_debug_ping", "IRC Debug Option: Show pings in console", "edit", "options", "0", "true:false", "1;0");
	Setting::add($MODULE_NAME, "irc_debug_messages", "IRC Debug Option: Show events in console", "edit", "options", "0", "true:false", "1;0");
	Setting::add($MODULE_NAME, "irc_password", "IRC password to join channel", "edit", "text", "none", "none");
	Setting::add($MODULE_NAME, "irc_debug_all", "IRC Debug Option: Log everything", "edit", "options", "0", "true;false", "1;0");
	
	// Helpfiles
	Help::register($MODULE_NAME, "irc", "irc_help.txt", "all", "How to use the IRC plugin");
?>
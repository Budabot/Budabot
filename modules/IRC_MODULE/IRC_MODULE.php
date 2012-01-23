<?php

   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
	require_once 'functions.php';
	require_once 'IRC.class.php';

	$channel = $setting->get('irc_channel');
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

	$event->register($MODULE_NAME, "1min", "set_irc_link.php", "Automatically reconnect to IRC server", '', 0);
	
	// Commands
	$command->register($MODULE_NAME, "", "irc_connect.php", "startirc", "mod", "Connect to IRC", "irc_help.txt");
	$command->register($MODULE_NAME, "", "stopirc.php", "stopirc", "mod", "Disconnect from IRC", "irc_help.txt");
	$command->register($MODULE_NAME, "", "online_irc.php", "onlineirc", "all", "View who is in IRC channel", "irc_help.txt");
	$command->register($MODULE_NAME, "", "set_irc_settings.php", "setirc", "mod", "Manually set IRC settings", "irc_help.txt");
	
	// IRC Relay
  	$event->register($MODULE_NAME, "2sec", "irc_check.php", "Receive messages from IRC");
	
	// In-game relay
	$event->register($MODULE_NAME, "priv", "relay_irc_out.php", "Relay (priv) messages to IRC");
	$event->register($MODULE_NAME, "guild", "relay_irc_out.php", "Relay (guild) messages to IRC");
	
	// Notifications
	$event->register($MODULE_NAME, "joinPriv", "irc_relay_joined.php", "Sends joined channel messages");
	$event->register($MODULE_NAME, "leavePriv", "irc_relay_left.php", "Sends left channel messages");
	$event->register($MODULE_NAME, "logOn", "irc_relay_joined.php", "Shows a logon from a member");
	$event->register($MODULE_NAME, "logOff", "irc_relay_left.php", "Shows a logoff from a member");
	
	// Settings
	$setting->add($MODULE_NAME, "irc_status", "Status of IRC uplink", "noedit", "options", "0", "Offline;Online", "0;1");
	$setting->add($MODULE_NAME, "irc_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "irc.funcom.com");
	$setting->add($MODULE_NAME, "irc_port", "IRC server port to use", "noedit", "number", "6667", "6667");
	$setting->add($MODULE_NAME, "irc_nickname", "Nickname to use while in IRC", "noedit", "text", $chatBot->vars['name'], $chatBot->vars['name']);
	$setting->add($MODULE_NAME, "irc_channel", "Channel to join", "noedit", "text", $channel, $channel);
	$setting->add($MODULE_NAME, "irc_password", "IRC password to join channel", "edit", "text", "none", "none");
	$setting->add($MODULE_NAME, 'irc_guild_message_color', "Color of messages from other bots in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
	$setting->add($MODULE_NAME, 'irc_guild_name_color', "Color of guild names from other bots in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
	$setting->add($MODULE_NAME, 'irc_message_color', "Color of messages from users in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
	$setting->add($MODULE_NAME, 'irc_ignore', "Defines which characters to ignore", 'edit', "text", 'none', 'none', '', '', 'irc_ignore.txt');
?>
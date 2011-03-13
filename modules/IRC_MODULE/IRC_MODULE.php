<?php

   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   ** Version = 0.2
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */

	$MODULE_NAME = "IRC_MODULE";
	if($chatBot->settings['irc_channel'] == "") {
		if($chatBot->vars['my_guild'] == "") {
			$channel = "#".strtolower($chatBot->vars['name']);
		}
		else {
			if(strpos($chatBot->vars['my_guild']," ")) {
			$sandbox = explode(" ",$chatBot->vars['my_guild']);
				for ($i = 0; $i < count($sandbox); $i++) {
					$channel .= ucfirst(strtolower($sandbox[$i]));
				}
				$channel = "#".$channel;
			}
			else {
				$channel = "#".$chatBot->vars['my_guild'];
			}
		}
	}

	//Auto start IRC connection, or turn it off
	Event::register($MODULE_NAME, "connect", "set_irc_link.php", "none", "Sets IRC status at bootup.");
	
	//Commands
	Command::register($MODULE_NAME, "", "irc_connect.php", "startirc", "admin", "Connect to IRC", 'irc');
	Command::register($MODULE_NAME, "", "stopirc.php", "stOPirc", "admin", "Disconnect from IRC", 'irc');
	Command::register($MODULE_NAME, "", "online_irc.php", "onlineirc", "all", "View who is in IRC chat", 'irc');
	
	//Command settings
	Command::register($MODULE_NAME, "", "set_irc_settings.php", "setirc", "admin", "Manually set IRC settings", 'irc');
	
	//IRC Relay
  	Event::register($MODULE_NAME, "2sec", "irc_check.php", "none", "Receive messages from IRC");
	
	//In-game relay
	Event::register($MODULE_NAME, "priv", "relay_irc_out.php", "none", "Relay (priv) messages to IRC");
	Event::register($MODULE_NAME, "guild", "relay_irc_out.php", "none", "Relay (guild) messages to IRC");
	
	//Notifications
	Event::register($MODULE_NAME, "joinPriv", "irc_relay_joined.php", "none", "Sends joined channel messages");
	Event::register($MODULE_NAME, "leavePriv", "irc_relay_left.php", "none", "Sends left channel messages");
	Event::register($MODULE_NAME, "logOn", "irc_relay_joined.php", "none", "Shows a logon from a member");
	Event::register($MODULE_NAME, "logOff", "irc_relay_left.php", "none", "Shows a logoff from a member");
	
	//Settings
	Setting::add($MODULE_NAME, "irc_status", "Status of IRC uplink", "noedit", "options", "0", "Offline;Online", "0;1", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "", "", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_port", "IRC server port to use", "noedit", "number", "6667", "", "", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_nickname", "Nickname to use while in IRC", "noedit", "text", $chatBot->vars['name'], "", "", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_channel", "Channel to join", "noedit", "text", $channel, "", "", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_autoconnect", "Connect to IRC at bootup", "edit", "options", "0", "true;false", "1;0", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_debug_ping", "IRC Debug Option: Show pings in console", "edit", "options", "0", "true:false", "1;0", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_debug_messages", "IRC Debug Option: Show events in console", "edit", "options", "0", "true:false", "1;0", "mod", "irc");
	Setting::add($MODULE_NAME, "irc_debug_all", "IRC Debug Option: Log everything", "edit", "options", "0", "true;false", "1;0", "mod", "irc");
	
	//Helpfiles
	Help::register($MODULE_NAME, "irc", "irc_help.txt", "all", "How to use the IRC plugin");
?>
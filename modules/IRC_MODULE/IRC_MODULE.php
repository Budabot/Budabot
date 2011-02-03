<?php

   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   ** Version = 0.2
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */

	$MODULE_NAME = "IRC_MODULE";
	if($this->settings['irc_channel'] == "") {
		if($this->vars['my guild'] == "") {
			$channel = "#".strtolower($this->vars['name']);
		}
		else {
			if(strpos($this->vars['my guild']," ")) {
			$sandbox = explode(" ",$this->vars['my guild']);
				for ($i = 0; $i < count($sandbox); $i++) {
					$channel .= ucfirst(strtolower($sandbox[$i]));
				}
				$channel = "#".$channel;
			}
			else {
				$channel = "#".$this->vars['my guild'];
			}
		}
	}

	//Auto start IRC connection, or turn it off
	Event::register($MODULE_NAME, "connect", "set_irc_link.php", "none", "Sets IRC status at bootup.");
	
	//Commands
	bot::command("msg", "$MODULE_NAME/irc_connect.php", "startirc", "admin", "Connect to IRC");
	bot::command("", "$MODULE_NAME/online_irc.php", "onlineirc", "all", "View who is in IRC chat");
	
	//Command settings
	bot::command("msg", "$MODULE_NAME/set_irc_settings.php", "setirc", "admin", "Manually set IRC settings");
	
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
	Setting::add($MODULE_NAME, "irc_status", "Status of IRC uplink", "noedit", "0", "Offline;Online", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_server", "IRC server to connect to", "noedit", "irc.funcom.com", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_port", "IRC server port to use", "noedit", "6667", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_nickname", "Nickname to use while in IRC", "noedit", "{$this->vars['name']}", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_channel", "Channel to join", "noedit", "$channel", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_autoconnect", "Connect to IRC at bootup", "edit", "0", "No;Yes", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_debug_ping", "IRC Debug Option: Show pings in console", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_debug_messages", "IRC Debug Option: Show events in console", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	Setting::add($MODULE_NAME, "irc_debug_all", "IRC Debug Option: Log everything", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	
	//Helpfiles
	bot::help($MODULE_NAME, "irc", "irc_help.txt", "all", "How to use the IRC plugin", "IRC Relay");
?>
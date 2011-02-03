<?php

   /*
   ** Author: Mindrila (RK1)
   ** Credits: Legendadv (RK2)
   ** BUDABOT IRC NETWORK MODULE
   ** Version = 0.1
   ** Developed for: Budabot(http://budabot.com)
   **
   */

	$MODULE_NAME = "BBIN_MODULE";
	if($this->settings['bbin_channel'] == "") {
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
	// Setup
	bot::loadSQLFile($MODULE_NAME, "bbin_chatlist");
	
	//Auto start BBIN connection, or turn it off
	bot::event($MODULE_NAME, "connect", "set_bbin_link.php", "none", "Sets BBIN status at bootup.");
	
	//Commands
	bot::command("msg", "$MODULE_NAME/bbin_connect.php", "startbbin", "admin", "Connect to BBIN");
	
	//Command settings
	bot::command("msg", "$MODULE_NAME/set_bbin_settings.php", "setbbin", "admin", "Manually set BBIN settings");
	
	//BBIN Relay
	bot::event($MODULE_NAME, "2sec", "bbin_loop.php", "none", "The main BBIN message loop");
	
	//In-game relay
	bot::event($MODULE_NAME, "priv", "relay_bbin_out.php", "none", "Relay (priv) messages to BBIN");
	bot::event($MODULE_NAME, "guild", "relay_bbin_out.php", "none", "Relay (guild) messages to BBIN");
	
	//Notifications
	bot::event($MODULE_NAME, "joinPriv", "bbin_relay_joined.php", "none", "Sends joined channel messages");
	bot::event($MODULE_NAME, "leavePriv", "bbin_relay_left.php", "none", "Sends left channel messages");
	bot::event($MODULE_NAME, "logOn", "bbin_relay_joined.php", "none", "Shows a logon from a member");
	bot::event($MODULE_NAME, "logOff", "bbin_relay_left.php", "none", "Shows a logoff from a member");
	
	//Settings
	Setting::add($MODULE_NAME, "bbin_status", "Status of BBIN uplink", "noedit", "0", "Offline;Online", "0;1", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_server", "IRC server to connect to", "noedit", "irc.funcom.com", "none", "0", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_port", "IRC server port to use", "noedit", "6667", "none", "0", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_nickname", "Nickname to use while in IRC", "noedit", "{$this->vars['name']}", "none", "0", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_channel", "Channel to join", "noedit", "$channel", "none", "0", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_autoconnect", "Connect to IRC at bootup", "edit", "0", "No;Yes", "0;1", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_debug_ping", "IRC Debug Option: Show pings in console", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_debug_messages", "IRC Debug Option: Show events in console", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_debug_all", "IRC Debug Option: Log everything", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/bbin_help.txt");
	
	//Helpfiles
	bot::help($MODULE_NAME, "bbin", "bbin_help.txt", "all", "How to use the BBIN plugin", "BBIN");
?>
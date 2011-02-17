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
	if($chatBot->settings['bbin_channel'] == "") {
		if($chatBot->vars['my guild'] == "") {
			$channel = "#".strtolower($chatBot->vars['name']);
		}
		else {
			if(strpos($chatBot->vars['my guild']," ")) {
			$sandbox = explode(" ",$chatBot->vars['my guild']);
				for ($i = 0; $i < count($sandbox); $i++) {
					$channel .= ucfirst(strtolower($sandbox[$i]));
				}
				$channel = "#".$channel;
			}
			else {
				$channel = "#".$chatBot->vars['my guild'];
			}
		}
	}
	// Setup
	DB::loadSQLFile($MODULE_NAME, "bbin_chatlist");
	
	//Auto start BBIN connection, or turn it off
	Event::register($MODULE_NAME, "connect", "set_bbin_link.php", "none", "Sets BBIN status at bootup.");
	
	//Commands
	Command::register($MODULE_NAME, "msg", "bbin_connect.php", "startbbin", "admin", "Connect to BBIN");
	
	//Command settings
	Command::register($MODULE_NAME, "msg", "set_bbin_settings.php", "setbbin", "admin", "Manually set BBIN settings");
	
	//BBIN Relay
	Event::register($MODULE_NAME, "2sec", "bbin_loop.php", "none", "The main BBIN message loop");
	
	//In-game relay
	Event::register($MODULE_NAME, "priv", "relay_bbin_out.php", "none", "Relay (priv) messages to BBIN");
	Event::register($MODULE_NAME, "guild", "relay_bbin_out.php", "none", "Relay (guild) messages to BBIN");
	
	//Notifications
	Event::register($MODULE_NAME, "joinPriv", "bbin_relay_joined.php", "none", "Sends joined channel messages");
	Event::register($MODULE_NAME, "leavePriv", "bbin_relay_left.php", "none", "Sends left channel messages");
	Event::register($MODULE_NAME, "logOn", "bbin_relay_joined.php", "none", "Shows a logon from a member");
	Event::register($MODULE_NAME, "logOff", "bbin_relay_left.php", "none", "Shows a logoff from a member");
	
	//Settings
	Setting::add($MODULE_NAME, "bbin_status", "Status of BBIN uplink", "noedit", "options", "0", "Offline;Online", "0;1", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "", "", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_port", "IRC server port to use", "noedit", "number", "6667", "", "", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_nickname", "Nickname to use while in IRC", "noedit", "text", $chatBot->vars['name'], "", "", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_channel", "Channel to join", "noedit", "text", $channel, "", "", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_autoconnect", "Connect to IRC at bootup", "edit", "options", "0", "true;false", "1;0", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_debug_ping", "IRC Debug Option: Show pings in console", "edit", "options", "0", "true;false", "1;0", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_debug_messages", "IRC Debug Option: Show events in console", "edit", "options", "0", "true;false", "1;0", "mod", "$MODULE_NAME/bbin_help.txt");
	Setting::add($MODULE_NAME, "bbin_debug_all", "IRC Debug Option: Log everything", "edit", "options", "0", "true;false", "1;0", "mod", "$MODULE_NAME/bbin_help.txt");
	
	//Helpfiles
	Help::register($MODULE_NAME, "bbin", "bbin_help.txt", "all", "How to use the BBIN plugin", "BBIN");
?>
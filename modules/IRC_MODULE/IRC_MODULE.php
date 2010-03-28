<?

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
	bot::event("connect", "$MODULE_NAME/set_irc_link.php", "none", "Sets IRC status at bootup.");
	
	//Commands
	bot::command("msg", "$MODULE_NAME/irc_connect.php", "startirc", "admin", "Connect to IRC");
	bot::command("msg", "$MODULE_NAME/online_irc.php", "onlineirc", "all", "View who is in IRC chat");
	bot::command("priv", "$MODULE_NAME/online_irc.php", "onlineirc", "all", "View who is in IRC chat");
	bot::command("guild", "$MODULE_NAME/online_irc.php", "onlineirc", "all", "View who is in IRC chat");
	
	//IRC Relay
  	bot::event("2sec", "IRC_MODULE/irc_check.php", "none", "Receive messages from IRC");
	
	//In-game relay
	bot::event("priv", "$MODULE_NAME/relay_irc_out.php", "none", "Relay (priv) messages to IRC");
	bot::event("guild", "$MODULE_NAME/relay_irc_out.php", "none", "Relay (guild) messages to IRC");
	
	//Notifications
	bot::event("joinPriv", "$MODULE_NAME/irc_relay_joined.php", "none", "Sends joined channel messages");
	bot::event("leavePriv", "$MODULE_NAME/irc_relay_left.php", "none", "Sends left channel messages");
	bot::event("logOn", "$MODULE_NAME/irc_relay_joined.php", "none", "Shows a logon from a member");
	bot::event("logOff", "$MODULE_NAME/irc_relay_left.php", "none", "Shows a logoff from a member");
	
	//Settings
	bot::addsetting("irc_status", "Status of IRC uplink", "noedit", "0", "Offline;Online", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	bot::addsetting("irc_server", "IRC server to connect to", "noedit", "irc.funcom.com", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	bot::addsetting("irc_port", "IRC server port to use", "noedit", "6667", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	bot::addsetting("irc_nickname", "Nickname to use while in IRC", "noedit", "{$this->vars['name']}", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	bot::addsetting("irc_channel", "Channel to join", "noedit", "$channel", "none", "0", "mod", "$MODULE_NAME/irc_help.txt");
	bot::addsetting("irc_autoconnect", "Connect to IRC at bootup", "edit", "0", "No;Yes", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	bot::addsetting("irc_debug_ping", "IRC Debug Option: Show pings in console", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	bot::addsetting("irc_debug_messages", "IRC Debug Option: Show events in console", "edit", "0", "Off;On", "0;1", "mod", "$MODULE_NAME/irc_help.txt");
	
	//Command settings
	bot::command("msg", "$MODULE_NAME/set_irc_settings.php", "setirc", "admin", "Manually set IRC settings");
	
	//Helpfiles
	bot::help("irc", "$MODULE_NAME/irc_help.txt", "all", "How to use the IRC plugin", "IRC Relay v$version");
?>
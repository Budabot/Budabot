<?php
	$MODULE_NAME = "RELAY_MODULE";
	$PLUGIN_VERSION = 1.0;
	
	// Guild relay using pgroup
	bot::command("msg", "$MODULE_NAME/send_relay_message.php", "@test", "mod", "Test relay");

	bot::event("guild", "$MODULE_NAME/send_relay_message.php", "none");
	bot::event("extJoinPrivRequest", "$MODULE_NAME/invite.php", "none", "");
	bot::event("extPriv", "$MODULE_NAME/receive_relay_message.php", "none", "");
	bot::event("msg", "$MODULE_NAME/tell_messages.php", 'none', 'Handles incoming tells from externalrelaybot');
	
	bot::addsetting("externalrelaybot", "Bot for external relay", "edit", "unknown", "text", '0', "mod", "");
	bot::addsetting("externalrelaysymbol", "Symbol for external relay", "edit", "@", "!;#;*;@;$;+;-", '0', "mod", "");
	
	// Guild relay using tells
  	bot::event("guild", "$MODULE_NAME/relay_guild_out.php", "grc", "Send Guildrelay messages");
	bot::event("logOn", "$MODULE_NAME/relay_guild_logon.php", "grc", "Sends Logon messages");
	bot::event("logOff", "$MODULE_NAME/relay_guild_logoff.php", "grc", "Sends Logoff messages");
  	bot::command("msg", "$MODULE_NAME/relay_guild_inc.php", "grc", "all", "Relays incoming messages to guildchat");
  	bot::command("msg", "$MODULE_NAME/relay_guild_cfg.php", "guildrelay", "all", "Relay between guildchats.");
  	bot::command("priv", "$MODULE_NAME/relay_guild_cfg.php", "guildrelay", "all", "Relay between guildchats.");
	bot::regGroup("Guild_Relay", $MODULE_NAME, "Relay Chat between guilds", "grc", "guildrelay");
	bot::addsetting("relaybot", "Bot for Guildrelay", "noedit", "0", "none", '0', "mod", "$MODULE_NAME/relaybot_help.txt");
	
	//Helpfiles
	bot::help("guildrelay", "$MODULE_NAME/guildrelay.txt", "guild", "How to relay chats between two guilds", "Org Commands");
?>
<?php
	$MODULE_NAME = "RELAY_MODULE";
	
	require_once 'functions.php';
	
	// Sending messages to relay
	bot::event("guild", "$MODULE_NAME/send_relay_message.php", "none");
	
	bot::command("", "$MODULE_NAME/tellrelay.php", "tellrelay", "admin", "Convenience command to quickly set up org relay over tells between two orgs");
	
	// Receiving messages to relay
	bot::command("msg", "$MODULE_NAME/receive_relay_message.php", "grc", "all", "Relays incoming messages to guildchat");
	bot::event("extPriv", "$MODULE_NAME/receive_relay_message.php", "none", "");

	// Inivite for pgroup
	bot::event("extJoinPrivRequest", "$MODULE_NAME/invite.php", "none", "");
	
	// Logon and Logoff messages
	bot::event("logOn", "$MODULE_NAME/relay_guild_logon.php", "none", "Sends Logon messages");
	bot::event("logOff", "$MODULE_NAME/relay_guild_logoff.php", "none", "Sends Logoff messages");
	
	// Org Messages
	bot::event("orgmsg", "$MODULE_NAME/org_messages.php", "none", "Relay Org Messages");
	
	// Settings
	bot::addsetting("relaytype", "Type of relay", "edit", "1", "tell;pgroup", '1;2', "mod", "");
	bot::addsetting("relaysymbol", "Symbol for external relay", "edit", "@", "!;#;*;@;$;+;-;Always relay", '0', "mod", "");
	bot::addsetting("relaybot", "Bot for Guildrelay", "edit", "Off", "text", '0', "mod");
	
	bot::help("tellrelay", "$MODULE_NAME/tellrelay.txt", "admin", "How to setup an org relay between two orgs using tells");
?>
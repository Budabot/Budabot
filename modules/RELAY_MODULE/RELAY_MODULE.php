<?php
	$MODULE_NAME = "RELAY_MODULE";
	
	require_once 'functions.php';
	
	// Sending messages to relay
	bot::event("guild", "$MODULE_NAME/send_relay_message.php", "none", "Sends org chat to relay");
	bot::event("priv", "$MODULE_NAME/send_relay_message.php", "none", "Sends private channel chat to relay");
	
	bot::command("", "$MODULE_NAME/tellrelay.php", "tellrelay", "admin", "Convenience command to quickly set up org relay over tells between two orgs");
	
	// Receiving messages to relay
	bot::command("msg", "$MODULE_NAME/receive_relay_message.php", "grc", "all", "Relays incoming messages to guildchat");
	bot::event("extPriv", "$MODULE_NAME/receive_relay_message.php", "none", "");

	// Inivite for pgroup
	bot::event("extJoinPrivRequest", "$MODULE_NAME/invite.php", "none", "");
	
	// Logon and Logoff messages
	bot::event("logOn", "$MODULE_NAME/relay_guild_logon.php", "none", "Sends Logon messages");
	bot::event("logOff", "$MODULE_NAME/relay_guild_logoff.php", "none", "Sends Logoff messages");
	
	// Private channel joins and leaves
	bot::event("joinPriv", "$MODULE_NAME/relay_priv_join.php", "none", "Sends a message to the relay when someone joins the private channel");
	bot::event("leavePriv", "$MODULE_NAME/relay_priv_leave.php", "none", "Sends a message to the relay when someone leaves the private channel");
	
	// Org Messages
	bot::event("orgmsg", "$MODULE_NAME/org_messages.php", "none", "Relay Org Messages");
	
	// Settings
	bot::addsetting($MODULE_NAME, "relaytype", "Type of relay", "edit", "1", "tell;pgroup", '1;2', "mod", "");
	bot::addsetting($MODULE_NAME, "relaysymbol", "Symbol for external relay", "edit", "@", "!;#;*;@;$;+;-;Always relay", '0', "mod", "");
	bot::addsetting($MODULE_NAME, "relaybot", "Bot for Guildrelay", "edit", "Off", "text", '0', "mod");
	
	bot::help("tellrelay", "$MODULE_NAME/tellrelay.txt", "admin", "How to setup an org relay between two orgs using tells");
?>
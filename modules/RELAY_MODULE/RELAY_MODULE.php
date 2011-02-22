<?php
	$MODULE_NAME = "RELAY_MODULE";
	
	require_once 'functions.php';
	
	// Sending messages to relay
	Event::register($MODULE_NAME, "guild", "send_relay_message.php", "none", "Sends org chat to relay");
	Event::register($MODULE_NAME, "priv", "send_relay_message.php", "none", "Sends private channel chat to relay");
	
	Command::register($MODULE_NAME, "", "tellrelay.php", "tellrelay", "mod", "Convenience command to quickly set up org relay over tells between two orgs");
	
	// Receiving messages to relay
	Command::register($MODULE_NAME, "msg", "receive_relay_message.php", "grc", "all", "Relays incoming messages to guildchat");
	Event::register($MODULE_NAME, "extPriv", "receive_relay_message.php", "none", "");

	// Inivite for private channel
	Event::register($MODULE_NAME, "extJoinPrivRequest", "invite.php", "none", "");
	
	// Logon and Logoff messages
	Event::register($MODULE_NAME, "logOn", "relay_guild_logon.php", "none", "Sends Logon messages");
	Event::register($MODULE_NAME, "logOff", "relay_guild_logoff.php", "none", "Sends Logoff messages");
	
	// Private channel joins and leaves
	Event::register($MODULE_NAME, "joinPriv", "relay_priv_join.php", "none", "Sends a message to the relay when someone joins the private channel");
	Event::register($MODULE_NAME, "leavePriv", "relay_priv_leave.php", "none", "Sends a message to the relay when someone leaves the private channel");
	
	// Org Messages
	Event::register($MODULE_NAME, "orgmsg", "org_messages.php", "none", "Relay Org Messages");
	
	// Settings
	Setting::add($MODULE_NAME, "relaytype", "Type of relay", "edit", "options", "1", "tell;private channel", '1;2', "mod", "");
	Setting::add($MODULE_NAME, "relaysymbol", "Symbol for external relay", "edit", "options", "@", "!;#;*;@;$;+;-;Always relay", '', "mod", "");
	Setting::add($MODULE_NAME, "relaybot", "Bot for Guildrelay", "edit", "text", "Off", "Off", '', "mod", "$MODULE_NAME/relaybot.txt");
	Setting::add($MODULE_NAME, "bot_relay_commands", "Relay commands and results over the bot relay", "edit", "options", "0", "true;false", "1;0");
	
	Help::register($MODULE_NAME, "tellrelay", "tellrelay.txt", "mod", "How to setup an org relay between two orgs using tells");
	Help::register($MODULE_NAME, "relaybot", "relaybot.txt", "mod", "Set the bot that this bot will relay with");
?>
<?php
	$MODULE_NAME = "RELAY_MODULE";
	$PLUGIN_VERSION = 1.0;
	
	require_once("functions.php");
	
	// Sending messages to relay
	$this->event("guild", "$MODULE_NAME/send_relay_message.php", "none");
	
	// Receiving messages to relay
	$this->command("msg", "$MODULE_NAME/receive_relay_message.php", "grc", ALL, "Relays incoming messages to guildchat");
	$this->event("extPriv", "$MODULE_NAME/receive_relay_message.php", "none", "");

	// Inivite for pgroup
	$this->event("extJoinPrivRequest", "$MODULE_NAME/invite.php", "none", "");
	
	// Logon and Logoff messages
	$this->event("logOn", "$MODULE_NAME/relay_guild_logon.php", "grc", "Sends Logon messages");
	$this->event("logOff", "$MODULE_NAME/relay_guild_logoff.php", "grc", "Sends Logoff messages");
	
	// Relay org messages between orgs
	$this->event("orgmsg", "$MODULE_NAME/org_messages.php", "none", "Relay Org Messages");
	
	// Settings
	$this->addsetting("relaytype", "Type of relay", "edit", "1", "tell;pgroup", '1;2', MODERATOR, "");
	$this->addsetting("relaysymbol", "Symbol for external relay", "edit", "@", "!;#;*;@;$;+;-;Always relay", '0', MODERATOR, "");
	$this->addsetting("relaybot", "Bot for Guildrelay", "edit", "Off", "text", '0', MODERATOR);
?>
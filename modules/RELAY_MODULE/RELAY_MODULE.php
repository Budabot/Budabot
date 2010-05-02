<?php
	$MODULE_NAME = "RELAY_MODULE";
	$PLUGIN_VERSION = 1.0;
	
	bot::command("msg", "$MODULE_NAME/send_relay_message.php", "@test", "mod", "Test relay");

	bot::event("guild", "$MODULE_NAME/send_relay_message.php", "none");
	bot::event("extJoinPrivRequest", "$MODULE_NAME/invite.php", "none", "");
	bot::event("extPriv", "$MODULE_NAME/receive_relay_message.php", "none", "");
	bot::event("msg", "$MODULE_NAME/tell_messages.php", 'none', 'Handles incoming tells from externalrelaybot');
	
	bot::addsetting("externalrelaybot", "Bot for external relay", "edit", "unknown", "text", '0', "mod", "");
	bot::addsetting("externalrelaysymbol", "Symbol for external relay", "edit", "@", "!;#;*;�;$;+;-", '0', "mod", "");
?>
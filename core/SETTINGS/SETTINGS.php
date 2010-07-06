<?php
	$MODULE_NAME = "SETTINGS";
	$PLUGIN_VERSION = 0.1;

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/bot_settings.php", "settings", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/bot_settings.php", "settings", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/bot_settings.php", "settings", MODERATOR);

	//Setup
	bot::regevent("setup", "$MODULE_NAME/upload_settings.php");
		
	//Help Files
	bot::help("settings", "$MODULE_NAME/settings.txt", MODERATOR, "Change Settings of the Bot.");
?>
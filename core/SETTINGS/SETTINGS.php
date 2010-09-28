<?php
	$MODULE_NAME = "SETTINGS";

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	bot::regcommand("priv", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	bot::regcommand("guild", "$MODULE_NAME/bot_settings.php", "settings", "mod");

	//Setup
	bot::regevent("setup", "$MODULE_NAME/setup.php");
	bot::regevent("setup", "$MODULE_NAME/upload_settings.php");
		
	//Help Files
	bot::help("settings", "$MODULE_NAME/settings.txt", "mod", "Change Settings of the Bot");
?>
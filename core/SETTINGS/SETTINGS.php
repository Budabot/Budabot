<?php
	$MODULE_NAME = "SETTINGS";
	$PLUGIN_VERSION = 0.1;

	//Commands
	$this->regcommand("msg", "$MODULE_NAME/bot_settings.php", "settings", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/bot_settings.php", "settings", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/bot_settings.php", "settings", MODERATOR);

	//Setup
	$this->regevent("setup", "$MODULE_NAME/upload_settings.php");
		
	//Help Files
	$this->help("settings", "$MODULE_NAME/settings.txt", MODERATOR, "Change Settings of the Bot.");
?>
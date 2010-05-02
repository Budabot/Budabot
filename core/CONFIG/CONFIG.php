<?php
$MODULE_NAME = "CONFIG";
$PLUGIN_VERSION = 0.1;

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/cmdcfg.php", "config", "mod");

	//Help Files
	bot::help("config", "$MODULE_NAME/config.txt", "mod", "Configure Commands/Events of the Bot.", "Configuration of the Bot");
?>
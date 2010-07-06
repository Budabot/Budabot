<?php 
	$MODULE_NAME = "HELP";
	$PLUGIN_VERSION = 0.1;

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/general_help.php", "about", ALL);
	bot::regcommand("guild", "$MODULE_NAME/general_help.php", "about", ALL);
	bot::regcommand("priv", "$MODULE_NAME/general_help.php", "about", ALL);
	bot::regcommand("msg", "$MODULE_NAME/general_help.php", "help", ALL);
	bot::regcommand("guild", "$MODULE_NAME/general_help.php", "help", ALL);
	bot::regcommand("priv", "$MODULE_NAME/general_help.php", "help", ALL);
	
	//Help Files
	bot::help("about", "$MODULE_NAME/about.txt", ALL, "Some Basic infos about the bot.");
?>
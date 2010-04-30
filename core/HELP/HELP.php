<?php 
$MODULE_NAME = "HELP";
$PLUGIN_VERSION = 0.1;

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/general_help.php", "about");
	bot::regcommand("guild", "$MODULE_NAME/general_help.php", "about");
	bot::regcommand("priv", "$MODULE_NAME/general_help.php", "about");
	bot::regcommand("msg", "$MODULE_NAME/general_help.php", "help");
	bot::regcommand("guild", "$MODULE_NAME/general_help.php", "help");
	bot::regcommand("priv", "$MODULE_NAME/general_help.php", "help");
	
	//Help Files
	bot::help("about", "$MODULE_NAME/about.txt", "all", "Some Basic infos about the bot.", "General");
?>
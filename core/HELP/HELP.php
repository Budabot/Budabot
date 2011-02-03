<?php 
	$MODULE_NAME = "HELP";

	//Commands
	Command::activate("msg", "$MODULE_NAME/general_help.php", "about");
	Command::activate("guild", "$MODULE_NAME/general_help.php", "about");
	Command::activate("priv", "$MODULE_NAME/general_help.php", "about");
	Command::activate("msg", "$MODULE_NAME/general_help.php", "help");
	Command::activate("guild", "$MODULE_NAME/general_help.php", "help");
	Command::activate("priv", "$MODULE_NAME/general_help.php", "help");
	
	//Help Files
	bot::help($MODULE_NAME, "about", "about.txt", "all", "Some Basic info about the bot");
?>
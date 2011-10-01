<?php 
	Command::activate("msg", "$MODULE_NAME/general_help.php", "about", 'all');
	Command::activate("guild", "$MODULE_NAME/general_help.php", "about", 'all');
	Command::activate("priv", "$MODULE_NAME/general_help.php", "about", 'all');
	Command::activate("msg", "$MODULE_NAME/general_help.php", "help", 'all');
	Command::activate("guild", "$MODULE_NAME/general_help.php", "help", 'all');
	Command::activate("priv", "$MODULE_NAME/general_help.php", "help", 'all');
	
	Help::register($MODULE_NAME, "about", "about.txt", "all", "Some Basic info about the bot");
?>
<?php 
	$command->activate("msg", "$MODULE_NAME/general_help.php", "about", 'all');
	$command->activate("guild", "$MODULE_NAME/general_help.php", "about", 'all');
	$command->activate("priv", "$MODULE_NAME/general_help.php", "about", 'all');
	$command->activate("msg", "$MODULE_NAME/general_help.php", "help", 'all');
	$command->activate("guild", "$MODULE_NAME/general_help.php", "help", 'all');
	$command->activate("priv", "$MODULE_NAME/general_help.php", "help", 'all');
	
	Help::register($MODULE_NAME, "about", "about.txt", "all", "Some Basic info about the bot");
?>
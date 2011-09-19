<?php
	$MODULE_NAME = "CONFIG";

	//Commands
	Command::activate("msg", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	Command::activate("guild", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	Command::activate("priv", "$MODULE_NAME/cmdcfg.php", "config", "mod");

	Command::register($MODULE_NAME, "", "remalias.php", "remalias", "mod", "Remove a command alias", 'alias');
	Command::register($MODULE_NAME, "", "addalias.php", "addalias", "mod", "Add a command alias", 'alias');
	Command::register($MODULE_NAME, "", "searchcmd.php", "searchcmd", "mod", "Find which module has specified command");

	//Help Files
	Help::register($MODULE_NAME, "config", "config.txt", "mod", "Configure Commands/Events of the Bot");
	Help::register($MODULE_NAME, "alias", "alias.txt", "mod", "How to add and remove aliases");
	// TODO add help for searchcmd, addalias/remalias	
?>
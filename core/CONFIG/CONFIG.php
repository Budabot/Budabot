<?php
	$MODULE_NAME = "CONFIG";

	//Commands
	Command::activate("msg", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	Command::activate("guild", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	Command::activate("priv", "$MODULE_NAME/cmdcfg.php", "config", "mod");

	Command::activate("msg", "$MODULE_NAME/searchcmd.php", "searchcmd", "mod");
	Command::activate("guild", "$MODULE_NAME/searchcmd.php", "searchcmd", "mod");
	Command::activate("priv", "$MODULE_NAME/searchcmd.php", "searchcmd", "mod");

	//Help Files
	bot::help($MODULE_NAME, "config", "config.txt", "mod", "Configure Commands/Events of the Bot");
?>
<?php
	$MODULE_NAME = "CONFIG";

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	bot::regcommand("guild", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	bot::regcommand("priv", "$MODULE_NAME/cmdcfg.php", "config", "mod");

	bot::regcommand("msg", "$MODULE_NAME/searchcmd.php", "searchcmd", "mod");
	bot::regcommand("guild", "$MODULE_NAME/searchcmd.php", "searchcmd", "mod");
	bot::regcommand("priv", "$MODULE_NAME/searchcmd.php", "searchcmd", "mod");

	//Help Files
	bot::help("config", "$MODULE_NAME/config.txt", "mod", "Configure Commands/Events of the Bot");
?>
<?php
$MODULE_NAME = "CONFIG";
$PLUGIN_VERSION = 0.1;

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/cmdcfg.php", "config", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/cmdcfg.php", "config", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/cmdcfg.php", "config", MODERATOR);

	bot::regcommand("msg", "$MODULE_NAME/searchcmd.php", "searchcmd", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/searchcmd.php", "searchcmd", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/searchcmd.php", "searchcmd", MODERATOR);
	
	bot::regcommand("msg", "$MODULE_NAME/cmdlist.php", "cmdlist", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/cmdlist.php", "cmdlist", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/cmdlist.php", "cmdlist", MODERATOR);

	//Help Files
	bot::help("config", "$MODULE_NAME/config.txt", MODERATOR, "Configure Commands/Events of the Bot.");
?>
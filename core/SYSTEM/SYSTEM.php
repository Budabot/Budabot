<?php 
	$MODULE_NAME = "SYSTEM";
	$PLUGIN_VERSION = 0.1;

	//Load extended messages
	bot::loadSQLFile($MODULE_NAME, "mmdb");

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/plugins.php", "newplugins", ADMIN);
	bot::regcommand("priv", "$MODULE_NAME/plugins.php", "newplugins", ADMIN);
	bot::regcommand("guild", "$MODULE_NAME/plugins.php", "newplugins", ADMIN);
	
	bot::regcommand("msg", "$MODULE_NAME/reboot.php", "reboot", ADMIN);
	bot::regcommand("priv", "$MODULE_NAME/reboot.php", "reboot", ADMIN);
	bot::regcommand("guild", "$MODULE_NAME/reboot.php", "reboot", ADMIN);	
	
	bot::regcommand("msg", "$MODULE_NAME/shutdown.php", "shutdown", ADMIN);
	bot::regcommand("priv", "$MODULE_NAME/shutdown.php", "shutdown", ADMIN);
	bot::regcommand("guild", "$MODULE_NAME/shutdown.php", "shutdown", ADMIN);
	
	bot::regcommand("msg", "$MODULE_NAME/uptime.php", "uptime", MEMBER);
	bot::regcommand("priv", "$MODULE_NAME/uptime.php", "uptime", MEMBER);
	bot::regcommand("guild", "$MODULE_NAME/uptime.php", "uptime", MEMBER);

	//Help Files
	bot::help("systemhelp", "$MODULE_NAME/system.txt", ADMIN, "Admin System Help file.");
?>
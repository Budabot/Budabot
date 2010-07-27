<?php 
	$MODULE_NAME = "SYSTEM";
	$PLUGIN_VERSION = 0.1;

	//Load extended messages
	bot::loadSQLFile($MODULE_NAME, "mmdb");

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	bot::regcommand("priv", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	bot::regcommand("guild", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	
	bot::regcommand("msg", "$MODULE_NAME/reboot.php", "reboot", "admin");
	bot::regcommand("priv", "$MODULE_NAME/reboot.php", "reboot", "admin");
	bot::regcommand("guild", "$MODULE_NAME/reboot.php", "reboot", "admin");	
	
	bot::regcommand("msg", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	bot::regcommand("priv", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	bot::regcommand("guild", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	
	bot::regcommand("msg", "$MODULE_NAME/uptime.php", "uptime", "all");
	bot::regcommand("priv", "$MODULE_NAME/uptime.php", "uptime", "all");
	bot::regcommand("guild", "$MODULE_NAME/uptime.php", "uptime", "all");
	
	bot::regcommand("msg", "$MODULE_NAME/memory.php", "memory", "all");
	bot::regcommand("priv", "$MODULE_NAME/memory.php", "memory", "all");
	bot::regcommand("guild", "$MODULE_NAME/memory.php", "memory", "all");

	//Help Files
	bot::help("systemhelp", "$MODULE_NAME/system.txt", "admin", "Admin System Help file.", "Administration");
?>
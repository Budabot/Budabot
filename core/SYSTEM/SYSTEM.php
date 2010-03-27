<?php 
$MODULE_NAME = "SYSTEM";
$PLUGIN_VERSION = 0.1;

	//Load extended messages
	bot::loadSQLFile($MODULE_NAME, "mmdb");

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	bot::regcommand("msg", "$MODULE_NAME/reboot.php", "reboot", "admin");
	bot::regcommand("msg", "$MODULE_NAME/shutdown.php", "shutdown", "admin");		

	//Help Files
	bot::help("systemhelp", "$MODULE_NAME/system.txt", "admin", "Admin System Help file.", "Administration");
?>
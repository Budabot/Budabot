<?php 
	$MODULE_NAME = "SYSTEM";

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

	bot::regcommand("msg", "$MODULE_NAME/cmdlist.php", "cmdlist", "mod");
	bot::regcommand("priv", "$MODULE_NAME/cmdlist.php", "cmdlist", "mod");
	bot::regcommand("guild", "$MODULE_NAME/cmdlist.php", "cmdlist", "mod");

	bot::regcommand("msg", "$MODULE_NAME/eventlist.php", "eventlist", "mod");
	bot::regcommand("priv", "$MODULE_NAME/eventlist.php", "eventlist", "mod");
	bot::regcommand("guild", "$MODULE_NAME/eventlist.php", "eventlist", "mod");

	bot::regcommand("msg", "$MODULE_NAME/lookup.php", "lookup", "mod");
	bot::regcommand("priv", "$MODULE_NAME/lookup.php", "lookup", "mod");
	bot::regcommand("guild", "$MODULE_NAME/lookup.php", "lookup", "mod");

	bot::regevent("1hour", "$MODULE_NAME/ping_db.php");

	//Help Files
	bot::help("system", "$MODULE_NAME/system.txt", "admin", "Admin System Help file");
?>
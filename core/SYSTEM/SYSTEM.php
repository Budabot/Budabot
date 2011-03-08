<?php 
	$MODULE_NAME = "SYSTEM";

	//Commands
	Command::activate("msg", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	Command::activate("priv", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	Command::activate("guild", "$MODULE_NAME/plugins.php", "newplugins", "admin");

	Command::activate("msg", "$MODULE_NAME/restart.php", "restart", "admin");
	Command::activate("priv", "$MODULE_NAME/restart.php", "restart", "admin");
	Command::activate("guild", "$MODULE_NAME/restart.php", "restart", "admin");	

	Command::activate("msg", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	Command::activate("priv", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	Command::activate("guild", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	
	Command::activate("msg", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");
	Command::activate("priv", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");
	Command::activate("guild", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");

	Command::activate("msg", "$MODULE_NAME/system_cmd.php", "system", "mod");
	Command::activate("priv", "$MODULE_NAME/system_cmd.php", "system", "mod");
	Command::activate("guild", "$MODULE_NAME/system_cmd.php", "system", "mod");

	Command::activate("msg", "$MODULE_NAME/cmdlist.php", "cmdlist", "guild");
	Command::activate("priv", "$MODULE_NAME/cmdlist.php", "cmdlist", "guild");
	Command::activate("guild", "$MODULE_NAME/cmdlist.php", "cmdlist", "guild");

	Command::activate("msg", "$MODULE_NAME/eventlist.php", "eventlist", "mod");
	Command::activate("priv", "$MODULE_NAME/eventlist.php", "eventlist", "mod");
	Command::activate("guild", "$MODULE_NAME/eventlist.php", "eventlist", "mod");

	Command::activate("msg", "$MODULE_NAME/lookup.php", "lookup", "mod");
	Command::activate("priv", "$MODULE_NAME/lookup.php", "lookup", "mod");
	Command::activate("guild", "$MODULE_NAME/lookup.php", "lookup", "mod");
	
	Command::activate("msg", "$MODULE_NAME/clearqueue.php", "clearqueue", "mod");
	Command::activate("priv", "$MODULE_NAME/clearqueue.php", "clearqueue", "mod");
	Command::activate("guild", "$MODULE_NAME/clearqueue.php", "clearqueue", "mod");
	
	Command::activate("msg", "$MODULE_NAME/loadsql.php", "loadsql", "mod");
	Command::activate("priv", "$MODULE_NAME/loadsql.php", "loadsql", "mod");
	Command::activate("guild", "$MODULE_NAME/loadsql.php", "loadsql", "mod");

	Event::activate("1hour", "$MODULE_NAME/ping_db.php");

	//Help Files
	Help::register($MODULE_NAME, "system", "system.txt", "admin", "Admin System Help file");
?>
<?php 
	$MODULE_NAME = "SYSTEM";
	$PLUGIN_VERSION = 0.1;

	//Load extended messages
	$this->loadSQLFile($MODULE_NAME, "mmdb");

	//Commands
	$this->regcommand("msg", "$MODULE_NAME/plugins.php", "newplugins", ADMIN);
	$this->regcommand("priv", "$MODULE_NAME/plugins.php", "newplugins", ADMIN);
	$this->regcommand("guild", "$MODULE_NAME/plugins.php", "newplugins", ADMIN);
	
	$this->regcommand("msg", "$MODULE_NAME/reboot.php", "reboot", ADMIN);
	$this->regcommand("priv", "$MODULE_NAME/reboot.php", "reboot", ADMIN);
	$this->regcommand("guild", "$MODULE_NAME/reboot.php", "reboot", ADMIN);	
	
	$this->regcommand("msg", "$MODULE_NAME/shutdown.php", "shutdown", ADMIN);
	$this->regcommand("priv", "$MODULE_NAME/shutdown.php", "shutdown", ADMIN);
	$this->regcommand("guild", "$MODULE_NAME/shutdown.php", "shutdown", ADMIN);
	
	$this->regcommand("msg", "$MODULE_NAME/uptime.php", "uptime", MEMBER);
	$this->regcommand("priv", "$MODULE_NAME/uptime.php", "uptime", MEMBER);
	$this->regcommand("guild", "$MODULE_NAME/uptime.php", "uptime", MEMBER);

	//Help Files
	$this->help("systemhelp", "$MODULE_NAME/system.txt", ADMIN, "Admin System Help file.");
?>
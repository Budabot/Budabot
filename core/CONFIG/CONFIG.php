<?php
$MODULE_NAME = "CONFIG";
$PLUGIN_VERSION = 0.1;

	//Commands
	$this->regcommand("msg", "$MODULE_NAME/cmdcfg.php", "config", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/cmdcfg.php", "config", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/cmdcfg.php", "config", MODERATOR);

	$this->regcommand("msg", "$MODULE_NAME/searchcmd.php", "searchcmd", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/searchcmd.php", "searchcmd", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/searchcmd.php", "searchcmd", MODERATOR);
	
	$this->regcommand("msg", "$MODULE_NAME/cmdlist.php", "cmdlist", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/cmdlist.php", "cmdlist", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/cmdlist.php", "cmdlist", MODERATOR);

	//Help Files
	$this->help("config", "$MODULE_NAME/config.txt", MODERATOR, "Configure Commands/Events of the Bot.");
?>
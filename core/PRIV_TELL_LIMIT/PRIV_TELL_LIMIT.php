<?php
	$MODULE_NAME = "PRIV_TELL_LIMIT";
	
	require_once 'Whitelist.class.php';
	
	DB::loadSqlFile($MODULE_NAME, 'whitelist');
	
	//Set/Show Limits
	Command::activate("msg", "$MODULE_NAME/config.php", "limits", "mod");
	Command::activate("msg", "$MODULE_NAME/config.php", "limit", "mod");
	Command::activate("msg", "$MODULE_NAME/whitelist.php", "whitelist", "mod");
	
	Command::activate("priv", "$MODULE_NAME/config.php", "limits", "mod");
	Command::activate("priv", "$MODULE_NAME/config.php", "limit", "mod");
	Command::activate("priv", "$MODULE_NAME/whitelist.php", "whitelist", "mod");
	
	Command::activate("guild", "$MODULE_NAME/config.php", "limits", "mod");
	Command::activate("guild", "$MODULE_NAME/config.php", "limit", "mod");
	Command::activate("guild", "$MODULE_NAME/whitelist.php", "whitelist", "mod");

	//Set/Show minlvl for Tells
	Command::activate("msg", "$MODULE_NAME/set_limits_tells.php", "tminlvl", "mod");
	Command::activate("priv", "$MODULE_NAME/set_limits_tells.php", "tminlvl", "mod");

	//Set/Show general limit for Tells
	Command::activate("msg", "$MODULE_NAME/set_limits_tells.php", "topen", "mod");
	Command::activate("priv", "$MODULE_NAME/set_limits_tells.php", "topen", "mod");

	//Set/Show faction limit for Tells
	Command::activate("msg", "$MODULE_NAME/set_limits_tells.php", "tfaction", "mod");
	Command::activate("priv", "$MODULE_NAME/set_limits_tells.php", "tfaction", "mod");

	//Set/Show minlvl for private channel
	Command::activate("msg", "$MODULE_NAME/set_limits_priv.php", "minlvl", "mod");
	Command::activate("priv", "$MODULE_NAME/set_limits_priv.php", "minlvl", "mod");

	//Set/Show general limit for private channel
	Command::activate("msg", "$MODULE_NAME/set_limits_priv.php", "openchannel", "mod");
	Command::activate("priv", "$MODULE_NAME/set_limits_priv.php", "openchannel", "mod");

	//Set/Show faction limit for private channel
	Command::activate("msg", "$MODULE_NAME/set_limits_priv.php", "faction", "mod");
	Command::activate("priv", "$MODULE_NAME/set_limits_priv.php", "faction", "mod");

	//Settings
	Setting::add($MODULE_NAME, "priv_req_lvl", "Private Channel Min Level Limit", "noedit", "number", "0", "", "", "mod", "$MODULE_NAME/help_minlvl.txt");
	Setting::add($MODULE_NAME, "priv_req_faction", "Private Channel Faction Limit", "noedit", "options", "all", "", "", "mod", "$MODULE_NAME/help_faction.txt");
	Setting::add($MODULE_NAME, "priv_req_open", "Private Channel General Limit", "noedit", "options", "all", "", "", "mod", "$MODULE_NAME/help_open.txt");
	Setting::add($MODULE_NAME, "priv_req_maxplayers", "Maximum number of players in the Private Channel", "noedit", "number", "0", "", "", "mod", "$MODULE_NAME/help_maxplayers.txt");

	Setting::add($MODULE_NAME, "tell_req_lvl", "Tells Min Level", "noedit", "number", "0", "", "", "mod", "$MODULE_NAME/help_tminlvl.txt");
	Setting::add($MODULE_NAME, "tell_req_faction", "Tell Faction Limit", "noedit", "options", "all", "", "", "mod", "$MODULE_NAME/help_tfaction.txt");
	Setting::add($MODULE_NAME, "tell_req_open", "Tell General Limit", "noedit", "options", "all", "", "", "mod", "$MODULE_NAME/help_topen.txt");

	//Help File
	Help::register($MODULE_NAME, "priv_tell_limits", "help.txt", "mod", "Set Limits for Tells and Private Channel");
?>
<?php
	$MODULE_NAME = "PRIV_TELL_LIMIT";
	
	require_once 'Whitelist.class.php';
	
	bot::loadSqlFile($MODULE_NAME, 'whitelist');
	
	//Set/Show Limits
	bot::regcommand("msg", "$MODULE_NAME/config.php", "limits", "mod");
	bot::regcommand("msg", "$MODULE_NAME/config.php", "limit", "mod");
	bot::regcommand("msg", "$MODULE_NAME/whitelist.php", "whitelist", "mod");
	
	bot::regcommand("priv", "$MODULE_NAME/config.php", "limits", "mod");
	bot::regcommand("priv", "$MODULE_NAME/config.php", "limit", "mod");
	bot::regcommand("priv", "$MODULE_NAME/whitelist.php", "whitelist", "mod");
	
	bot::regcommand("guild", "$MODULE_NAME/config.php", "limits", "mod");
	bot::regcommand("guild", "$MODULE_NAME/config.php", "limit", "mod");
	bot::regcommand("guild", "$MODULE_NAME/whitelist.php", "whitelist", "mod");

	//Set/Show minlvl for Tells
	bot::regcommand("msg", "$MODULE_NAME/set_limits_tells.php", "tminlvl", "mod");
	bot::regcommand("priv", "$MODULE_NAME/set_limits_tells.php", "tminlvl", "mod");

	//Set/Show general limit for Tells
	bot::regcommand("msg", "$MODULE_NAME/set_limits_tells.php", "topen", "mod");
	bot::regcommand("priv", "$MODULE_NAME/set_limits_tells.php", "topen", "mod");

	//Set/Show faction limit for Tells
	bot::regcommand("msg", "$MODULE_NAME/set_limits_tells.php", "tfaction", "mod");
	bot::regcommand("priv", "$MODULE_NAME/set_limits_tells.php", "tfaction", "mod");

	//Set/Show minlvl for privategroup
	bot::regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "minlvl", "mod");
	bot::regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "minlvl", "mod");

	//Set/Show general limit for privategroup
	bot::regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "open", "mod");
	bot::regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "open", "mod");

	//Set/Show faction limit for privategroup
	bot::regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "faction", "mod");
	bot::regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "faction", "mod");

	//Set/Show faction limit for privategroup
	bot::regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "faction", "mod");
	bot::regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "faction", "mod");

	//Settings
	bot::addsetting("priv_req_lvl", "Private Channel Min Level Limit", "noedit", "0", "none", "0", "mod", "$MODULE_NAME/help_minlvl.txt");
	bot::addsetting("priv_req_faction", "Private Channel Faction Limit", "noedit", "all", "none", "0", "mod", "$MODULE_NAME/help_faction.txt");
	bot::addsetting("priv_req_open", "Private Channel General Limit", "noedit", "all", "none", "0", "mod", "$MODULE_NAME/help_open.txt");
	bot::addsetting("priv_req_maxplayers", "Maximum Players in the PrivGroup", "noedit", "0", "none", "0", "mod", "$MODULE_NAME/help_maxplayers.txt");

	bot::addsetting("tell_req_lvl", "Tells Min Level", "noedit", "0", "none", "0", "mod", "$MODULE_NAME/help_tminlvl.txt");
	bot::addsetting("tell_req_faction", "Tell Faction Limit", "noedit", "all", "none", "0", "mod", "$MODULE_NAME/help_tfaction.txt");
	bot::addsetting("tell_req_open", "Tell General Limit", "noedit", "all", "none", "0", "mod", "$MODULE_NAME/help_topen.txt");

	//Help File
	bot::help("priv_tell_limits", "$MODULE_NAME/help.txt", "mod", "Set Limits for tells and PrivGroup.", "Configuration of the Bot");
?>
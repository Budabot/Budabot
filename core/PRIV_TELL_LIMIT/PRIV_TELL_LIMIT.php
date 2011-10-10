<?php
	require_once 'Whitelist.class.php';
	
	DB::loadSqlFile($MODULE_NAME, 'whitelist');
	
	//Set/Show Limits
	Command::activate("msg", "$MODULE_NAME/limits.php", "limits", "mod");
	Command::activate("priv", "$MODULE_NAME/limits.php", "limits", "mod");
	Command::activate("guild", "$MODULE_NAME/limits.php", "limits", "mod");
	
	Command::activate("msg", "$MODULE_NAME/whitelist.php", "whitelist", "mod");
	Command::activate("priv", "$MODULE_NAME/whitelist.php", "whitelist", "mod");
	Command::activate("guild", "$MODULE_NAME/whitelist.php", "whitelist", "mod");

	//Settings
	Setting::add($MODULE_NAME, "priv_req_lvl", "Private Channel Min Level Limit", "noedit", "number", "0", "", "", "mod");
	Setting::add($MODULE_NAME, "priv_req_faction", "Private Channel Faction Limit", "noedit", "options", "all", "", "", "mod");
	Setting::add($MODULE_NAME, "priv_req_maxplayers", "Maximum number of players in the Private Channel", "noedit", "number", "0", "", "", "mod");

	Setting::add($MODULE_NAME, "tell_req_lvl", "Tells Min Level", "noedit", "number", "0", "", "", "mod");
	Setting::add($MODULE_NAME, "tell_req_faction", "Tell Faction Limit", "noedit", "options", "all", "", "", "mod");
	Setting::add($MODULE_NAME, "tell_req_open", "Tell General Limit", "noedit", "options", "all", "", "", "mod");

	//Help File
	Help::register($MODULE_NAME, "limits", "limits.txt", "mod", "How to restrict usage of the bot");
	Help::register($MODULE_NAME, "whitelist", "whitelist.txt", "mod", "How to add exceptions to limits rules");
	Help::register($MODULE_NAME, "priv_req_lvl", "priv_req_lvl.txt", "mod", "Set level requirements to join the private channel");
	Help::register($MODULE_NAME, "priv_req_faction", "priv_req_faction.txt", "mod", "Set faction requirements to join the private channel");
	Help::register($MODULE_NAME, "priv_req_maxplayers", "priv_req_maxplayers.txt", "mod", "Set the maximum amount of players allowed in the private channel at a time");
	Help::register($MODULE_NAME, "tell_req_lvl", "tell_req_lvl.txt", "mod", "Set level requirements to send tells to the bot");
	Help::register($MODULE_NAME, "tell_req_faction", "tell_req_faction.txt", "mod", "Set faction requirements to send tells to the bot");
	Help::register($MODULE_NAME, "tell_req_open", "tell_req_open.txt", "mod", "Set general requirements to send tells to the bot");
?>
<?php
$MODULE_NAME = "PRIV_TELL_LIMIT";
	
	//Set/Show Limits
	$this->regcommand("msg", "$MODULE_NAME/config.php", "limits", MODERATOR);	
	$this->regcommand("msg", "$MODULE_NAME/config.php", "limit", MODERATOR);

	//Set/Show minlvl for Tells
	$this->regcommand("msg", "$MODULE_NAME/set_limits_tells.php", "tminlvl", MODERATOR);	
	$this->regcommand("priv", "$MODULE_NAME/set_limits_tells.php", "tminlvl", MODERATOR);

	//Set/Show general limit for Tells
	$this->regcommand("msg", "$MODULE_NAME/set_limits_tells.php", "topen", MODERATOR);	
	$this->regcommand("priv", "$MODULE_NAME/set_limits_tells.php", "topen", MODERATOR);

	//Set/Show faction limit for Tells
	$this->regcommand("msg", "$MODULE_NAME/set_limits_tells.php", "tfaction", MODERATOR);	
	$this->regcommand("priv", "$MODULE_NAME/set_limits_tells.php", "tfaction", MODERATOR);


	//Set/Show minlvl for privategroup
	$this->regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "minlvl", MODERATOR);	
	$this->regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "minlvl", MODERATOR);

	//Set/Show general limit for privategroup
	$this->regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "open", MODERATOR);	
	$this->regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "open", MODERATOR);

	//Set/Show faction limit for privategroup
	$this->regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "faction", MODERATOR);	
	$this->regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "faction", MODERATOR);

	//Set/Show faction limit for privategroup
	$this->regcommand("msg", "$MODULE_NAME/set_limits_priv.php", "faction", MODERATOR);	
	$this->regcommand("priv", "$MODULE_NAME/set_limits_priv.php", "faction", MODERATOR);

	//Settings
	$this->addsetting("priv_req_lvl", "Private Channel Min Level Limit", "noedit", "0", "none", "0", MODERATOR, "$MODULE_NAME/help_minlvl.txt");
	$this->addsetting("priv_req_faction", "Private Channel Faction Limit", "noedit", "all", "none", "0", MODERATOR, "$MODULE_NAME/help_faction.txt");
	$this->addsetting("priv_req_open", "Private Channel General Limit", "noedit", "all", "none", "0", MODERATOR, "$MODULE_NAME/help_open.txt");
	$this->addsetting("priv_req_maxplayers", "Maximum Players in the PrivGroup", "noedit", "0", "none", "0", MODERATOR, "$MODULE_NAME/help_maxplayers.txt");

	$this->addsetting("tell_req_lvl", "Tells Min Level", "noedit", "0", "none", "0", MODERATOR, "$MODULE_NAME/help_tminlvl.txt");
	$this->addsetting("tell_req_faction", "Tell Faction Limit", "noedit", "all", "none", "0", MODERATOR, "$MODULE_NAME/help_tfaction.txt");
	$this->addsetting("tell_req_open", "Tell General Limit", "noedit", "all", "none", "0", MODERATOR, "$MODULE_NAME/help_topen.txt");

	//Help File
	$this->help("priv_tell_limits", "$MODULE_NAME/help.txt", MODERATOR, "Set Limits for tells and PrivGroup.");
?>
<?php 
	$MODULE_NAME = "ADMIN";
	
	//Setup
	$this->regevent("setup", "$MODULE_NAME/upload_admins.php");

	//Commands	
	$this->regcommand("msg", "$MODULE_NAME/addadmin.php", "addadmin", ADMIN);
	$this->regcommand("priv", "$MODULE_NAME/addadmin.php", "addadmin", ADMIN);
	$this->regcommand("guild", "$MODULE_NAME/addadmin.php", "addadmin", ADMIN);
	
	$this->regcommand("msg", "$MODULE_NAME/kickadmin.php", "kickadmin", ADMIN);
	$this->regcommand("priv", "$MODULE_NAME/kickadmin.php", "kickadmin", ADMIN);
	$this->regcommand("guild", "$MODULE_NAME/kickadmin.php", "kickadmin", ADMIN);
	
	$this->regcommand("msg", "$MODULE_NAME/addmod.php", "addmod", ADMIN);
	$this->regcommand("priv", "$MODULE_NAME/addmod.php", "addmod", ADMIN);
	$this->regcommand("guild", "$MODULE_NAME/addmod.php", "addmod", ADMIN);
	
	$this->regcommand("msg", "$MODULE_NAME/kickmod.php", "kickmod", ADMIN);
	$this->regcommand("priv", "$MODULE_NAME/kickmod.php", "kickmod", ADMIN);
	$this->regcommand("guild", "$MODULE_NAME/kickmod.php", "kickmod", ADMIN);
	
	$this->regcommand("msg", "$MODULE_NAME/raidleader.php", "raidleader", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/raidleader.php", "raidleader", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/raidleader.php", "raidleader", MODERATOR);
	
	$this->regcommand("msg", "$MODULE_NAME/kickraidleader.php", "kickraidleader", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/kickraidleader.php", "kickraidleader", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/kickraidleader.php", "kickraidleader", MODERATOR);

	$this->regcommand("msg", "$MODULE_NAME/adminlist.php", "adminlist");
	$this->regcommand("priv", "$MODULE_NAME/adminlist.php", "adminlist");
	$this->regcommand("guild", "$MODULE_NAME/adminlist.php", "adminlist");

	//Events
	$this->regevent("logOn", "$MODULE_NAME/admin_logon.php");
	$this->regevent("logOff", "$MODULE_NAME/admin_logoff.php");
	$this->regevent("24hrs", "$MODULE_NAME/check_admins.php");

	//Help Files
	$this->help("adminhelp", "$MODULE_NAME/admin.txt", MODERATOR, "Mod/Admin Help file.");
?>
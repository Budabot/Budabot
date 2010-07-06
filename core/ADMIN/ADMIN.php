<?php 
	$MODULE_NAME = "ADMIN";
	
	//Setup
	bot::regevent("setup", "$MODULE_NAME/upload_admins.php");

	//Commands	
	bot::regcommand("msg", "$MODULE_NAME/addadmin.php", "addadmin", ADMIN);
	bot::regcommand("priv", "$MODULE_NAME/addadmin.php", "addadmin", ADMIN);
	bot::regcommand("guild", "$MODULE_NAME/addadmin.php", "addadmin", ADMIN);
	
	bot::regcommand("msg", "$MODULE_NAME/kickadmin.php", "kickadmin", ADMIN);
	bot::regcommand("priv", "$MODULE_NAME/kickadmin.php", "kickadmin", ADMIN);
	bot::regcommand("guild", "$MODULE_NAME/kickadmin.php", "kickadmin", ADMIN);
	
	bot::regcommand("msg", "$MODULE_NAME/addmod.php", "addmod", ADMIN);
	bot::regcommand("priv", "$MODULE_NAME/addmod.php", "addmod", ADMIN);
	bot::regcommand("guild", "$MODULE_NAME/addmod.php", "addmod", ADMIN);
	
	bot::regcommand("msg", "$MODULE_NAME/kickmod.php", "kickmod", ADMIN);
	bot::regcommand("priv", "$MODULE_NAME/kickmod.php", "kickmod", ADMIN);
	bot::regcommand("guild", "$MODULE_NAME/kickmod.php", "kickmod", ADMIN);
	
	bot::regcommand("msg", "$MODULE_NAME/raidleader.php", "raidleader", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/raidleader.php", "raidleader", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/raidleader.php", "raidleader", MODERATOR);
	
	bot::regcommand("msg", "$MODULE_NAME/kickraidleader.php", "kickraidleader", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/kickraidleader.php", "kickraidleader", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/kickraidleader.php", "kickraidleader", MODERATOR);

	bot::regcommand("msg", "$MODULE_NAME/adminlist.php", "adminlist");
	bot::regcommand("priv", "$MODULE_NAME/adminlist.php", "adminlist");
	bot::regcommand("guild", "$MODULE_NAME/adminlist.php", "adminlist");

	//Events
	bot::regevent("logOn", "$MODULE_NAME/admin_logon.php");
	bot::regevent("logOff", "$MODULE_NAME/admin_logoff.php");
	bot::regevent("24hrs", "$MODULE_NAME/check_admins.php");

	//Help Files
	bot::help("adminhelp", "$MODULE_NAME/admin.txt", MODERATOR, "Mod/Admin Help file.");
?>
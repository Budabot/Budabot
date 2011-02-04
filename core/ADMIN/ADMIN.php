<?php 
	$MODULE_NAME = "ADMIN";

	//Commands	
	Command::activate("msg", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	Command::activate("priv", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	Command::activate("guild", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	
	Command::activate("msg", "$MODULE_NAME/kickadmin.php", "kickadmin", "admin");
	Command::activate("priv", "$MODULE_NAME/kickadmin.php", "kickadmin", "admin");
	Command::activate("guild", "$MODULE_NAME/kickadmin.php", "kickadmin", "admin");
	
	Command::activate("msg", "$MODULE_NAME/addmod.php", "addmod", "admin");
	Command::activate("priv", "$MODULE_NAME/addmod.php", "addmod", "admin");
	Command::activate("guild", "$MODULE_NAME/addmod.php", "addmod", "admin");
	
	Command::activate("msg", "$MODULE_NAME/kickmod.php", "kickmod", "admin");
	Command::activate("priv", "$MODULE_NAME/kickmod.php", "kickmod", "admin");
	Command::activate("guild", "$MODULE_NAME/kickmod.php", "kickmod", "admin");
	
	Command::activate("msg", "$MODULE_NAME/raidleader.php", "raidleader", "mod");
	Command::activate("priv", "$MODULE_NAME/raidleader.php", "raidleader", "mod");
	Command::activate("guild", "$MODULE_NAME/raidleader.php", "raidleader", "mod");
	
	Command::activate("msg", "$MODULE_NAME/kickraidleader.php", "kickraidleader", "mod");
	Command::activate("priv", "$MODULE_NAME/kickraidleader.php", "kickraidleader", "mod");
	Command::activate("guild", "$MODULE_NAME/kickraidleader.php", "kickraidleader", "mod");

	Command::activate("msg", "$MODULE_NAME/adminlist.php", "adminlist");
	Command::activate("priv", "$MODULE_NAME/adminlist.php", "adminlist");
	Command::activate("guild", "$MODULE_NAME/adminlist.php", "adminlist");

	//Events
	Event::activate("logOn", "$MODULE_NAME/admin_logon.php");
	Event::activate("logOff", "$MODULE_NAME/admin_logoff.php");
	Event::activate("24hrs", "$MODULE_NAME/check_admins.php");

	//Setup
	Event::activate("setup", "$MODULE_NAME/upload_admins.php");

	//Help Files
	Help::register($MODULE_NAME, "admin", "admin.txt", "mod", "Mod/Admin Help file");
?>
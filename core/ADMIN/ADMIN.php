<?php 
	$MODULE_NAME = "ADMIN";

	//Commands	
	Command::activate("msg", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	Command::activate("priv", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	Command::activate("guild", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	
	Command::activate("msg", "$MODULE_NAME/remadmin.php", "remadmin", "admin");
	Command::activate("priv", "$MODULE_NAME/remadmin.php", "remadmin", "admin");
	Command::activate("guild", "$MODULE_NAME/remadmin.php", "remadmin", "admin");
	
	Command::activate("msg", "$MODULE_NAME/addmod.php", "addmod", "admin");
	Command::activate("priv", "$MODULE_NAME/addmod.php", "addmod", "admin");
	Command::activate("guild", "$MODULE_NAME/addmod.php", "addmod", "admin");
	
	Command::activate("msg", "$MODULE_NAME/remmod.php", "remmod", "admin");
	Command::activate("priv", "$MODULE_NAME/remmod.php", "remmod", "admin");
	Command::activate("guild", "$MODULE_NAME/remmod.php", "remmod", "admin");
	
	Command::activate("msg", "$MODULE_NAME/addrl.php", "addrl", "mod");
	Command::activate("priv", "$MODULE_NAME/addrl.php", "addrl", "mod");
	Command::activate("guild", "$MODULE_NAME/addrl.php", "addrl", "mod");
	
	Command::activate("msg", "$MODULE_NAME/remrl.php", "remrl", "mod");
	Command::activate("priv", "$MODULE_NAME/remrl.php", "remrl", "mod");
	Command::activate("guild", "$MODULE_NAME/remrl.php", "remrl", "mod");

	Command::activate("msg", "$MODULE_NAME/adminlist.php", "adminlist");
	Command::activate("priv", "$MODULE_NAME/adminlist.php", "adminlist");
	Command::activate("guild", "$MODULE_NAME/adminlist.php", "adminlist");

	//Events
	Event::activate("logOn", "$MODULE_NAME/admin_logon.php");
	Event::activate("logOff", "$MODULE_NAME/admin_logoff.php");
	Event::activate("24hrs", "$MODULE_NAME/check_admins.php");

	//Setup
	Event::activate("setup", "$MODULE_NAME/upload_admins.php");
	
	Setting::add($MODULE_NAME, 'alts_inherit_admin', 'Alts inherit admin privileges from main', 'edit', "options", 0, "true;false", "1;0", 'mod');

	//Help Files
	Help::register($MODULE_NAME, "admin", "admin.txt", "mod", "Mod/Admin Help file");
?>
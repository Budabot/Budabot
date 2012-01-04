<?php
	require_once 'Admin.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'Admin', new Admin);

	$command->activate("msg", "Admin.addCommand", "addadmin", "admin");
	$command->activate("priv", "Admin.addCommand", "addadmin", "admin");
	$command->activate("guild", "Admin.addCommand", "addadmin", "admin");
	
	$command->activate("msg", "Admin.removeCommand", "remadmin", "superadmin");
	$command->activate("priv", "Admin.removeCommand", "remadmin", "superadmin");
	$command->activate("guild", "Admin.removeCommand", "remadmin", "superadmin");
	
	$command->activate("msg", "Admin.addCommand", "addmod", "admin");
	$command->activate("priv", "Admin.addCommand", "addmod", "admin");
	$command->activate("guild", "Admin.addCommand", "addmod", "admin");
	
	$command->activate("msg", "Admin.removeCommand", "remmod", "admin");
	$command->activate("priv", "Admin.removeCommand", "remmod", "admin");
	$command->activate("guild", "Admin.removeCommand", "remmod", "admin");
	
	$command->activate("msg", "Admin.addCommand", "addrl", "mod");
	$command->activate("priv", "Admin.addCommand", "addrl", "mod");
	$command->activate("guild", "Admin.addCommand", "addrl", "mod");
	
	$command->activate("msg", "Admin.removeCommand", "remrl", "mod");
	$command->activate("priv", "Admin.removeCommand", "remrl", "mod");
	$command->activate("guild", "Admin.removeCommand", "remrl", "mod");

	$command->activate("msg", "$MODULE_NAME/adminlist.php", "adminlist");
	$command->activate("priv", "$MODULE_NAME/adminlist.php", "adminlist");
	$command->activate("guild", "$MODULE_NAME/adminlist.php", "adminlist");

	$event->activate("connect", "$MODULE_NAME/check_admins.php");
	$event->activate("setup", "$MODULE_NAME/upload_admins.php");

	$help->register($MODULE_NAME, "admin", "admin.txt", "mod", "Mod/admin help file");
	$help->register($MODULE_NAME, "alts_inherit_admin", "alts_inherit_admin.txt", "mod", "Alts inherit admin privileges from main");
?>
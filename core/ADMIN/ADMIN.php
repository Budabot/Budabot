<?php
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

	$command->activate("msg", "Admin.adminlistCommand", "adminlist", 'all');
	$command->activate("priv", "Admin.adminlistCommand", "adminlist", 'all');
	$command->activate("guild", "Admin.adminlistCommand", "adminlist", 'all');

	$event->activate("connect", "Admin.checkAdmins");
	$event->activate("setup", "Admin.uploadAdmins");

	$help->register($MODULE_NAME, "admin", "admin.txt", "mod", "Mod/admin help file");
	$help->register($MODULE_NAME, "alts_inherit_admin", "alts_inherit_admin.txt", "mod", "Alts inherit admin privileges from main");
?>
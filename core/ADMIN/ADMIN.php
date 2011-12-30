<?php 
	$command->activate("msg", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	$command->activate("priv", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	$command->activate("guild", "$MODULE_NAME/addadmin.php", "addadmin", "admin");
	
	$command->activate("msg", "$MODULE_NAME/remadmin.php", "remadmin", "superadmin");
	$command->activate("priv", "$MODULE_NAME/remadmin.php", "remadmin", "superadmin");
	$command->activate("guild", "$MODULE_NAME/remadmin.php", "remadmin", "superadmin");
	
	$command->activate("msg", "$MODULE_NAME/addmod.php", "addmod", "admin");
	$command->activate("priv", "$MODULE_NAME/addmod.php", "addmod", "admin");
	$command->activate("guild", "$MODULE_NAME/addmod.php", "addmod", "admin");
	
	$command->activate("msg", "$MODULE_NAME/remmod.php", "remmod", "admin");
	$command->activate("priv", "$MODULE_NAME/remmod.php", "remmod", "admin");
	$command->activate("guild", "$MODULE_NAME/remmod.php", "remmod", "admin");
	
	$command->activate("msg", "$MODULE_NAME/addrl.php", "addrl", "mod");
	$command->activate("priv", "$MODULE_NAME/addrl.php", "addrl", "mod");
	$command->activate("guild", "$MODULE_NAME/addrl.php", "addrl", "mod");
	
	$command->activate("msg", "$MODULE_NAME/remrl.php", "remrl", "mod");
	$command->activate("priv", "$MODULE_NAME/remrl.php", "remrl", "mod");
	$command->activate("guild", "$MODULE_NAME/remrl.php", "remrl", "mod");

	$command->activate("msg", "$MODULE_NAME/adminlist.php", "adminlist");
	$command->activate("priv", "$MODULE_NAME/adminlist.php", "adminlist");
	$command->activate("guild", "$MODULE_NAME/adminlist.php", "adminlist");

	Event::activate("connect", "$MODULE_NAME/check_admins.php");
	Event::activate("setup", "$MODULE_NAME/upload_admins.php");

	Help::register($MODULE_NAME, "admin", "admin.txt", "mod", "Mod/admin help file");
	Help::register($MODULE_NAME, "alts_inherit_admin", "alts_inherit_admin.txt", "mod", "Alts inherit admin privileges from main");
?>
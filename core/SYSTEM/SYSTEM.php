<?php 
	$MODULE_NAME = "SYSTEM";

	//Commands
	Command::activate("msg", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	Command::activate("priv", "$MODULE_NAME/plugins.php", "newplugins", "admin");
	Command::activate("guild", "$MODULE_NAME/plugins.php", "newplugins", "admin");

	Command::activate("msg", "$MODULE_NAME/restart.php", "restart", "admin");
	Command::activate("priv", "$MODULE_NAME/restart.php", "restart", "admin");
	Command::activate("guild", "$MODULE_NAME/restart.php", "restart", "admin");	

	Command::activate("msg", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	Command::activate("priv", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	Command::activate("guild", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	
	Command::activate("msg", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");
	Command::activate("priv", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");
	Command::activate("guild", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");

	Command::activate("msg", "$MODULE_NAME/system_cmd.php", "system", "mod");
	Command::activate("priv", "$MODULE_NAME/system_cmd.php", "system", "mod");
	Command::activate("guild", "$MODULE_NAME/system_cmd.php", "system", "mod");

	Command::activate("msg", "$MODULE_NAME/cmdlist.php", "cmdlist", "guild");
	Command::activate("priv", "$MODULE_NAME/cmdlist.php", "cmdlist", "guild");
	Command::activate("guild", "$MODULE_NAME/cmdlist.php", "cmdlist", "guild");

	Command::activate("msg", "$MODULE_NAME/eventlist.php", "eventlist", "mod");
	Command::activate("priv", "$MODULE_NAME/eventlist.php", "eventlist", "mod");
	Command::activate("guild", "$MODULE_NAME/eventlist.php", "eventlist", "mod");

	Command::activate("msg", "$MODULE_NAME/lookup.php", "lookup", "mod");
	Command::activate("priv", "$MODULE_NAME/lookup.php", "lookup", "mod");
	Command::activate("guild", "$MODULE_NAME/lookup.php", "lookup", "mod");
	
	Command::activate("msg", "$MODULE_NAME/clearqueue.php", "clearqueue", "mod");
	Command::activate("priv", "$MODULE_NAME/clearqueue.php", "clearqueue", "mod");
	Command::activate("guild", "$MODULE_NAME/clearqueue.php", "clearqueue", "mod");
	
	Command::activate("msg", "$MODULE_NAME/loadsql.php", "loadsql", "mod");
	Command::activate("priv", "$MODULE_NAME/loadsql.php", "loadsql", "mod");
	Command::activate("guild", "$MODULE_NAME/loadsql.php", "loadsql", "mod");

	Event::activate("1hour", "$MODULE_NAME/ping_db.php");
	
	Setting::add($MODULE_NAME, 'symbol', 'Command prefix symbol', 'edit', "text", '!', '!;#;*;@;$;+;-');
	Setting::add($MODULE_NAME, 'guild_admin_level', 'Guild admin level', 'edit', "number", 1, 'President;General;Squad Commander;Unit Commander;Unit Leader;Unit Member;Applicant', '0;1;2;3;4;5;6', 'mod');
	Setting::add($MODULE_NAME, 'spam_protection', 'Enable spam protection', 'edit', "options", 0, "true;false", "1;0", 'mod');
	Setting::add($MODULE_NAME, 'max_blob_size', 'Max chars for a window', 'edit', "number", 7500, '', '', 'mod');
	Setting::add($MODULE_NAME, 'cron_delay', 'How long to wait in seconds before executing cron jobs', 'edit', "number", 10, '5;10;20;30', '', 'mod');
	Setting::add($MODULE_NAME, 'guild_channel_status', 'Enable the guild channel', 'edit', "options", 1, "true;false", "1;0", 'mod');

	//Help Files
	Help::register($MODULE_NAME, "system", "system.txt", "admin", "Admin System Help file");
	Help::register($MODULE_NAME, "guild_admin_level", "guild_admin_level.txt", "mod", "Change what guild rank and high receives the guild admin level privilege");
	Help::register($MODULE_NAME, "spam_protection", "spam_protection.txt", "mod", "Enable or disable the spam protection");
	Help::register($MODULE_NAME, "max_blob_size", "max_blob_size.txt", "mod", "Set the maximum blob size");
	Help::register($MODULE_NAME, "default_module_status", "default_module_status.txt", "mod", "Set new modules to be enabled or disabled by default");
?>
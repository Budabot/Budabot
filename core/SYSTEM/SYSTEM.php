<?php 
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

	Command::activate("msg", "$MODULE_NAME/executesql.php", "executesql", "admin");
	Command::activate("priv", "$MODULE_NAME/executesql.php", "executesql", "admin");
	Command::activate("guild", "$MODULE_NAME/executesql.php", "executesql", "admin");
	
	Command::activate("msg", "$MODULE_NAME/logs.php", "logs", "admin");
	Command::activate("priv", "$MODULE_NAME/logs.php", "logs", "admin");
	Command::activate("guild", "$MODULE_NAME/logs.php", "logs", "admin");
	
	Command::register($MODULE_NAME, "", "clearqueue.php", "clearqueue", "mod", "Clear outgoing chatqueue from all pending messages");
	Command::register($MODULE_NAME, "", "loadsql.php", "loadsql", "mod", "Manually reload an sql file");
	Command::register($MODULE_NAME, "", "checkaccess.php", "checkaccess", "all", "Check effective access level of a character");
	Command::register($MODULE_NAME, "", "macro.php", "macro", "all", "Execute multiple commands at once");

	Event::activate("1hour", "$MODULE_NAME/ping_db.php");
	Event::activate("2sec", "$MODULE_NAME/reduce_spam_values.php");
	Event::activate("1min", "$MODULE_NAME/reduce_largespam_values.php");
	Event::activate("connect", "$MODULE_NAME/systems_ready.php");
	
	Setting::add($MODULE_NAME, 'symbol', 'Command prefix symbol', 'edit', "text", '!', '!;#;*;@;$;+;-');
	Setting::add($MODULE_NAME, 'guild_admin_rank', 'Guild rank required to be considered a guild admin', 'edit', "options", '1', '0;1;2;3;4;5;6');
	Setting::add($MODULE_NAME, 'guild_admin_access_level', 'Access level that guild admins acquire', 'edit', "options", 'all', 'admin;mod;rl;all');
	Setting::add($MODULE_NAME, 'spam_protection', 'Enable spam protection', 'edit', "options", 0, "true;false", "1;0");
	Setting::add($MODULE_NAME, 'max_blob_size', 'Max chars for a window', 'edit', "number", 7500, '4500;6000;7500;9000;10500;12000');
	Setting::add($MODULE_NAME, 'xml_timeout', 'Max time to wait for response from xml servers', 'edit', "time", '5s', '1s;2s;5s;10s;30s');
	Setting::add($MODULE_NAME, 'logon_delay', 'Time to wait before executing connect events and cron jobs', 'edit', "time", '20s', '5s;10s;20s;30s');
	Setting::add($MODULE_NAME, 'guild_channel_status', 'Enable the guild channel', 'edit', "options", 1, "true;false", "1;0");
	Setting::add($MODULE_NAME, 'guild_channel_cmd_feedback',   "Show message on invalid command in guild channel", 'edit', "options", 1, "true;false", "1;0");
	Setting::add($MODULE_NAME, 'private_channel_cmd_feedback', "Show message on invalid command in private channel", 'edit', "options", 1, "true;false", "1;0");
	Setting::add($MODULE_NAME, 'version', "Bot version that database was created from", 'noedit', "text", 0);
	
	global $version;
	Setting::save('version', $version);

	Help::register($MODULE_NAME, "system", "system.txt", "admin", "Admin System Help file");
	Help::register($MODULE_NAME, "guild_admin_rank", "guild_admin_rank.txt", "mod", "Change what guild rank is considered a guild admin");
	Help::register($MODULE_NAME, "spam_protection", "spam_protection.txt", "mod", "Enable or disable the spam protection");
	Help::register($MODULE_NAME, "max_blob_size", "max_blob_size.txt", "mod", "Set the maximum blob size");
	Help::register($MODULE_NAME, "checkaccess", "checkaccess.txt", "all", "How to get effective access level of a character");
	Help::register($MODULE_NAME, "loadsql", "loadsql.txt", "mod", "How to manually load an sql file");
	Help::register($MODULE_NAME, "clearqueue", "clearqueue.txt", "mod", "How to clear the outgoing chat message queue");
	Help::register($MODULE_NAME, "budatime", "budatime.txt", "all", "Format for budatime");
	Help::register($MODULE_NAME, "macro", "macro.txt", "all", "How to execute multiple commands at once");
?>
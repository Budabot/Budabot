<?php
	require_once './lib/ReverseFileReader.class.php';

	$command->activate("msg", "$MODULE_NAME/restart.php", "restart", "admin");
	$command->activate("priv", "$MODULE_NAME/restart.php", "restart", "admin");
	$command->activate("guild", "$MODULE_NAME/restart.php", "restart", "admin");

	$command->activate("msg", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	$command->activate("priv", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	$command->activate("guild", "$MODULE_NAME/shutdown.php", "shutdown", "admin");
	
	$command->activate("msg", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");
	$command->activate("priv", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");
	$command->activate("guild", "$MODULE_NAME/reload_config.php", "reloadconfig", "admin");

	$command->activate("msg", "$MODULE_NAME/system_cmd.php", "system", "mod");
	$command->activate("priv", "$MODULE_NAME/system_cmd.php", "system", "mod");
	$command->activate("guild", "$MODULE_NAME/system_cmd.php", "system", "mod");

	$command->activate("msg", "$MODULE_NAME/executesql.php", "executesql", "admin");
	$command->activate("priv", "$MODULE_NAME/executesql.php", "executesql", "admin");
	$command->activate("guild", "$MODULE_NAME/executesql.php", "executesql", "admin");
	
	$command->activate("msg", "$MODULE_NAME/executesql.php", "querysql", "admin");
	$command->activate("priv", "$MODULE_NAME/executesql.php", "querysql", "admin");
	$command->activate("guild", "$MODULE_NAME/executesql.php", "querysql", "admin");
	
	$command->activate("msg", "$MODULE_NAME/logs.php", "logs", "admin");
	$command->activate("priv", "$MODULE_NAME/logs.php", "logs", "admin");
	$command->activate("guild", "$MODULE_NAME/logs.php", "logs", "admin");
	
	$command->register($MODULE_NAME, "", "clearqueue.php", "clearqueue", "mod", "Clear outgoing chatqueue from all pending messages", "clearqueue.txt", 1);
	$command->register($MODULE_NAME, "", "loadsql.php", "loadsql", "mod", "Manually reload an sql file", "loadsql.txt", 1);
	$command->register($MODULE_NAME, "", "checkaccess.php", "checkaccess", "all", "Check effective access level of a character", "checkaccess.txt", 1);
	$command->register($MODULE_NAME, "", "macro.php", "macro", "all", "Execute multiple commands at once", "macro.txt", 1);

	$event->activate("1hour", "$MODULE_NAME/ping_db.php");
	$event->activate("2sec", "$MODULE_NAME/reduce_spam_values.php");
	$event->activate("1min", "$MODULE_NAME/reduce_largespam_values.php");
	$event->activate("connect", "$MODULE_NAME/systems_ready.php");
	
	$setting->add($MODULE_NAME, 'symbol', 'Command prefix symbol', 'edit', "text", '!', '!;#;*;@;$;+;-');
	$setting->add($MODULE_NAME, 'guild_admin_rank', 'Guild rank required to be considered a guild admin', 'edit', "options", '1', '0;1;2;3;4;5;6', '', 'mod', "guild_admin_rank.txt");
	$setting->add($MODULE_NAME, 'guild_admin_access_level', 'Access level that guild admins acquire', 'edit', "options", 'all', 'admin;mod;rl;all');
	$setting->add($MODULE_NAME, 'spam_protection', 'Enable spam protection', 'edit', "options", 0, "true;false", "1;0", 'mod', "spam_protection.txt");
	$setting->add($MODULE_NAME, 'max_blob_size', 'Max chars for a window', 'edit', "number", 7500, '4500;6000;7500;9000;10500;12000', '', 'mod', "max_blob_size.txt");
	$setting->add($MODULE_NAME, 'xml_timeout', 'Max time to wait for response from xml servers', 'edit', "time", '5s', '1s;2s;5s;10s;30s');
	$setting->add($MODULE_NAME, 'logon_delay', 'Time to wait before executing connect events and cron jobs', 'edit', "time", '10s', '5s;10s;20s;30s');
	$setting->add($MODULE_NAME, 'guild_channel_status', 'Enable the guild channel', 'edit', "options", 1, "true;false", "1;0");
	$setting->add($MODULE_NAME, 'guild_channel_cmd_feedback',   "Show message on invalid command in guild channel", 'edit', "options", 1, "true;false", "1;0");
	$setting->add($MODULE_NAME, 'private_channel_cmd_feedback', "Show message on invalid command in private channel", 'edit', "options", 1, "true;false", "1;0");
	$setting->add($MODULE_NAME, 'version', "Bot version that database was created from", 'noedit', "text", 0);
	
	global $version;
	$setting->save('version', $version);

	$help->register($MODULE_NAME, "system", "system.txt", "admin", "Admin System Help file");
	$help->register($MODULE_NAME, "budatime", "budatime.txt", "all", "Format for budatime");
	$help->register($MODULE_NAME, "logs", "logs.txt", "all", "View bot logs");
?>
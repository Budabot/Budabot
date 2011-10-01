<?php
	require_once 'DB2.class.php';
	
	Command::register($MODULE_NAME, "", "migrate.php", "migrate", "admin", "Migrate database from a previous version of Budabot");
	
	Setting::add($MODULE_NAME, "migrate_type", "Database type to migrate from", "edit", "options", "Sqlite", "Sqlite;Mysql");
	Setting::add($MODULE_NAME, "migrate_name", "Database name to migrate from", "edit", "text", "budabot.db", "budabot.db");
	Setting::add($MODULE_NAME, "migrate_hostname", "Database hostname to migrate from", "edit", "text", "./modules/MIGRATE_MODULE/", "./modules/MIGRATE_MODULE/");
	Setting::add($MODULE_NAME, "migrate_username", "Database username to migrate from", "edit", "text", "");
	Setting::add($MODULE_NAME, "migrate_password", "Database password to migrate from", "edit", "text", "");
	Setting::add($MODULE_NAME, "migrate_botname", "Bot name to migrate from", "edit", "text", $chatBot->vars['name']);

	// Help files
    Help::register($MODULE_NAME, "migrate", "migrate.txt", "admin", "How to migrate your database from a previous version of Budabot");
?>

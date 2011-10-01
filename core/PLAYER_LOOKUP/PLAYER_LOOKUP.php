<?php
	require_once 'Player.class.php';
	require_once 'Guild.class.php';
	
	if ($db->get_type() == 'Mysql') {
		DB::loadSQLFile($MODULE_NAME, 'players_mysql');
	} else if ($db->get_type() == 'Sqlite') {
		DB::loadSQLFile($MODULE_NAME, 'players_sqlite');
	}
?>
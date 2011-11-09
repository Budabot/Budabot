<?php
	require_once 'Player.class.php';
	require_once 'Guild.class.php';
	
	if ($db->get_type() == 'mysql') {
		DB::loadSQLFile($MODULE_NAME, 'players_mysql');
	} else if ($db->get_type() == 'sqlite') {
		DB::loadSQLFile($MODULE_NAME, 'players_sqlite');
	}
?>
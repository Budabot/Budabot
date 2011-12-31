<?php
	require_once 'Player.class.php';
	require_once 'Guild.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'Player', new Player);
	$chatBot->registerInstance($MODULE_NAME, 'Guild', new Guild);
	
	if ($db->get_type() == 'mysql') {
		$db->loadSQLFile($MODULE_NAME, 'players_mysql');
	} else if ($db->get_type() == 'sqlite') {
		$db->loadSQLFile($MODULE_NAME, 'players_sqlite');
	}
?>
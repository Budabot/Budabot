<?php
	$MODULE_NAME = "PLAYER_LOOKUP";
	
	require_once 'Player.class.php';
	require_once 'Guild.class.php';
	
	DB::loadSQLFile($MODULE_NAME, 'players');
?>
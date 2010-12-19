<?php
	$MODULE_NAME = "PLAYER_LOOKUP";
	
	require_once 'Player.class.php';
	
	bot::loadSQLFile($MODULE_NAME, 'players');
?>
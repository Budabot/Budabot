<?php
	$MODULE_NAME = "PLAYER_LOOKUP";
	
	require_once 'Player.class.php';
	require_once 'Guild.class.php';
	
	//Setup
	Event::activate("setup", "$MODULE_NAME/players_table.php");
?>
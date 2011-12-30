<?php
	require_once 'functions.php';

	$db->loadSQLFile($MODULE_NAME, "spirits");
	
	$command->register($MODULE_NAME, "", "spirits.php", "spirits", "all", "Search for Spirits");
	$command->register($MODULE_NAME, "", "spirits.php", "spiritslvl", "all", "Search for Spirits", 'spirits');
	$command->register($MODULE_NAME, "", "spirits.php", "spiritsagi", "all", "Search for Spirits", 'spirits');
	$command->register($MODULE_NAME, "", "spirits.php", "spiritssen", "all", "Search for Spirits", 'spirits');
	
	Help::register($MODULE_NAME, "spirits", "spirits.txt", "all", "Search for Spirits");
	
?>
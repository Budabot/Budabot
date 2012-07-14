<?php
	require_once 'functions.php';

	$db->loadSQLFile($MODULE_NAME, "spirits");

	$command->register($MODULE_NAME, "", "spirits.php", "spirits", "all", "Search for Spirits", "spirits.txt");
	$command->register($MODULE_NAME, "", "spirits.php", "spiritslvl", "all", "Search for Spirits", "spirits.txt");
	$command->register($MODULE_NAME, "", "spirits.php", "spiritsagi", "all", "Search for Spirits", "spirits.txt");
	$command->register($MODULE_NAME, "", "spirits.php", "spiritssen", "all", "Search for Spirits", "spirits.txt");
?>

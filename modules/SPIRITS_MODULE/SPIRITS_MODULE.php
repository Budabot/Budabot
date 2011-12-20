<?php
	require_once 'functions.php';

	DB::loadSQLFile($MODULE_NAME, "spirits");
	
	Command::register($MODULE_NAME, "", "spirits.php", "spirits", "all", "Search for Spirits");
	Command::register($MODULE_NAME, "", "spirits.php", "spiritslvl", "all", "Search for Spirits", 'spirits');
	Command::register($MODULE_NAME, "", "spirits.php", "spiritsagi", "all", "Search for Spirits", 'spirits');
	Command::register($MODULE_NAME, "", "spirits.php", "spiritssen", "all", "Search for Spirits", 'spirits');
	
	Help::register($MODULE_NAME, "spirits", "spirits.txt", "all", "Search for Spirits");
	
?>
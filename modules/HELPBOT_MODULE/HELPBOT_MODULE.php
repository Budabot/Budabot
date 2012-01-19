<?php
	require_once 'db_utils.php';

	$db->loadSQLFile($MODULE_NAME, "buffitems");
	$db->loadSQLFile($MODULE_NAME, "roll");
	$db->loadSQLFile($MODULE_NAME, "koslist");
	$db->loadSQLFile($MODULE_NAME, "dyna");
	$db->loadSQLFile($MODULE_NAME, "research");
	$db->loadSQLFile($MODULE_NAME, "playfields");
	$db->loadSQLFile($MODULE_NAME, "orgcities");

	$command->register($MODULE_NAME, "", "bufftest.php", "bufftest", "all", "test");
	$command->register($MODULE_NAME, "", "kos.php", "kos", "guild", "Show the Kill On Sight List", "kos.txt");
	$command->register($MODULE_NAME, "", "time.php", "time", "all", "Show the time in the different timezones", "time.txt");
	$command->register($MODULE_NAME, "", "calc.php", "calc", "all", "Calculator", "calculator.txt");
	$command->register($MODULE_NAME, "", "oe.php", "oe", "all", "Over-equipped calculation", "oe.txt");
	$command->register($MODULE_NAME, "", "inspect.php", "inspect", "all", "Inspect Christmas/Eart Gifts and Peren. Containers", "inspect.txt");
	$command->register($MODULE_NAME, "", "mobloot.php", "mobloot", "all", "Show loot QL info", "mobloot.txt");
	$command->register($MODULE_NAME, "", "random.php", "random", "all", "Randomize a list of names/items", "random.txt");
	$command->register($MODULE_NAME, "", "buffitem.php", "buffitem", "all", "Buffitem look up", "buffitem.txt");
	$command->register($MODULE_NAME, "", "whatbuffs.php", "whatbuffs", "all", "Find items that buff", "whatbuffs.txt");
	$command->register($MODULE_NAME, "", "dyna.php", "dyna", "all", "Search for RK Dynaboss", "dyna.txt");
	$command->register($MODULE_NAME, "", "research.php", "research", "all", "Show info on Research", "research.txt");
	$command->register($MODULE_NAME, "", "playfields.php", "playfields", "all", "Show playfield ids, long names, and short names", "waypoint.txt");
	$command->register($MODULE_NAME, "", "waypoint.php", "waypoint", "all", "Create a waypoint link", "waypoint.txt");
	$command->register($MODULE_NAME, "", "orgcities.php", "orgcities", "all", "Show coords for org cities", "orgcities.txt");
	$command->register($MODULE_NAME, "", "server.php", "server", "all", "Show the Server status", "server.txt");

	// Flip or Roll command
	$command->register($MODULE_NAME, "", "roll.php", "flip", "all", "Flip a coin", "roll.txt");
	$command->register($MODULE_NAME, "", "roll.php", "roll", "all", "Roll a random number", "roll.txt");
	$command->register($MODULE_NAME, "", "roll.php", "verify", "all", "Verifies a flip/roll", "roll.txt");
?>

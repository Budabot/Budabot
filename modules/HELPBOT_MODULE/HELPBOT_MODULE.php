<?php
	require_once 'db_utils.php';

	$db->loadSQLFile($MODULE_NAME, "buffitems");
	$db->loadSQLFile($MODULE_NAME, "roll");
	$db->loadSQLFile($MODULE_NAME, "koslist");
	$db->loadSQLFile($MODULE_NAME, "dyna");
	$db->loadSQLFile($MODULE_NAME, "research");
	$db->loadSQLFile($MODULE_NAME, "playfields");
	$db->loadSQLFile($MODULE_NAME, "orgcities");

	$command->register($MODULE_NAME, "", "bufftest.php", "bufftest", "leader", "test");
	$command->register($MODULE_NAME, "", "kos.php", "kos", "guild", "Show the Kill On Sight List");
	$command->register($MODULE_NAME, "", "time.php", "time", "all", "Show the time in the different timezones");
	$command->register($MODULE_NAME, "", "calc.php", "calc", "all", "Calculator");
	$command->register($MODULE_NAME, "", "oe.php", "oe", "all", "OE");
	$command->register($MODULE_NAME, "", "inspect.php", "inspect", "all", "Inspect Christmas/Eart Gifts and Peren. Containers");
	$command->register($MODULE_NAME, "", "mobloot.php", "mobloot", "all", "Show loot QL info");
	$command->register($MODULE_NAME, "", "random.php", "random", "all", "Randomize a list of names/items");
	$command->register($MODULE_NAME, "", "buffitem.php", "buffitem", "all", "Buffitem look up");
	$command->register($MODULE_NAME, "", "whatbuffs.php", "whatbuffs", "all", "Find items that buff");
	$command->register($MODULE_NAME, "", "dyna.php", "dyna", "all", "Search for RK Dynaboss");
	$command->register($MODULE_NAME, "", "research.php", "research", "all", "Show info on Research");
	$command->register($MODULE_NAME, "", "playfields.php", "playfields", "all", "Show playfield ids, long names, and short names", 'waypoint');
	$command->register($MODULE_NAME, "", "waypoint.php", "waypoint", "all", "Create a waypoint link");
	$command->register($MODULE_NAME, "", "orgcities.php", "orgcities", "all", "Show coords for org cities");
	$command->register($MODULE_NAME, "", "server.php", "server", "all", "Show the Server status");

	// Flip or Roll command
	$command->register($MODULE_NAME, "", "roll.php", "flip", "all", "Flip a coin", 'roll');
	$command->register($MODULE_NAME, "", "roll.php", "roll", "all", "Roll a random number");
	$command->register($MODULE_NAME, "", "roll.php", "verify", "all", "Verifies a flip/roll", 'roll');

	// Help files
    $help->register($MODULE_NAME, "calc", "calculator.txt", "all", "Calculator");
    $help->register($MODULE_NAME, "oe", "oe.txt", "all", "Calculating the OE ranges");
    $help->register($MODULE_NAME, "roll", "roll.txt", "all", "How to use the flip and roll command");
    $help->register($MODULE_NAME, "time", "time.txt", "all", "Timezones");
    $help->register($MODULE_NAME, "kos", "kos.txt", "all", "Kill On Sight List");
    $help->register($MODULE_NAME, "inspect", "inspect.txt", "all", "How to use inspect");
	$help->register($MODULE_NAME, "buffitem", "buffitem.txt", "all", "How to use buffitem");
	$help->register($MODULE_NAME, "mobloot", "mobloot.txt", "all", "How to use mobloot");
	$help->register($MODULE_NAME, "whatbuffs", "whatbuffs.txt", "all", "How to use whatbuffs");
	$help->register($MODULE_NAME, "dyna", "dyna.txt", "all", "Search for RK Dynaboss");
	$help->register($MODULE_NAME, "research", "research.txt", "all", "Info on Research");
	$help->register($MODULE_NAME, "waypoint", "waypoint.txt", "all", "How to create a waypoint link");
	$help->register($MODULE_NAME, "orgcities", "orgcities.txt", "all", "How to find coords for org cities");
	$help->register($MODULE_NAME, "random", "random.txt", "all", "How to randomly order a list of elements");
	$help->register($MODULE_NAME, "server", "server.txt", "all", "Show the server status");
?>

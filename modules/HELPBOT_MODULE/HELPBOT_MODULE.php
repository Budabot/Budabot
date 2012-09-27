<?php
	require_once 'db_utils.php';
	require_once 'PlayfieldController.class.php';
	require_once 'OrgCitiesController.class.php';
	require_once 'ResearchController.class.php';
	require_once 'RollController.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'PlayfieldController', new PlayfieldController());
	$chatBot->registerInstance($MODULE_NAME, 'OrgCitiesController', new OrgCitiesController());
	$chatBot->registerInstance($MODULE_NAME, 'ResearchController', new ResearchController());
	$chatBot->registerInstance($MODULE_NAME, 'RollController', new RollController());

	$db->loadSQLFile($MODULE_NAME, "koslist");
	$db->loadSQLFile($MODULE_NAME, "dyna");

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
	$command->register($MODULE_NAME, "", "server.php", "server", "all", "Show the Server status", "server.txt");
?>

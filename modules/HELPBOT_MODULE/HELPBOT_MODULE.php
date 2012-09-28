<?php
	require_once 'db_utils.php';
	require_once 'PlayfieldController.class.php';
	require_once 'OrgCitiesController.class.php';
	require_once 'ResearchController.class.php';
	require_once 'RandomController.class.php';
	require_once 'ServerStatusController.class.php';
	require_once 'HelpbotController.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'PlayfieldController', new PlayfieldController());
	$chatBot->registerInstance($MODULE_NAME, 'OrgCitiesController', new OrgCitiesController());
	$chatBot->registerInstance($MODULE_NAME, 'ResearchController', new ResearchController());
	$chatBot->registerInstance($MODULE_NAME, 'RandomController', new RandomController());
	$chatBot->registerInstance($MODULE_NAME, 'ServerStatusController', new ServerStatusController());
	$chatBot->registerInstance($MODULE_NAME, 'HelpbotController', new HelpbotController());

	$command->register($MODULE_NAME, "", "time.php", "time", "all", "Show the time in the different timezones", "time.txt");
	$command->register($MODULE_NAME, "", "buffitem.php", "buffitem", "all", "Buffitem look up", "buffitem.txt");
	$command->register($MODULE_NAME, "", "whatbuffs.php", "whatbuffs", "all", "Find items that buff", "whatbuffs.txt");
?>

<?php
	require_once 'orglist_functions.php';
	require_once 'IsOnlineController.class.php';
	require_once 'OrgMembersController.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'IsOnlineController', new IsOnlineController());
	$chatBot->registerInstance($MODULE_NAME, 'OrgMembersController', new OrgMembersController());

	$command->register($MODULE_NAME, "", "orgranks.php", "orgranks", "guild", "Show guild members sorted by guild rank", "orgranks.txt");
	$command->register($MODULE_NAME, "", "whoisorg.php", "whoisorg", "all", "Display org info", "whoisorg.txt");
	$command->register($MODULE_NAME, "", "findorg.php", "findorg", "all", "Find orgs by name", "findorg.txt");
	$command->register($MODULE_NAME, "", "orglist.php", "orglist", "guild", "Check someones org roster", "orglist.txt");

	$event->register($MODULE_NAME, "logOn", "orglist_check.php", "Gets online status of org members");
	$event->register($MODULE_NAME, "logOff", "orglist_check.php", "Gets offline status of org members");
?>

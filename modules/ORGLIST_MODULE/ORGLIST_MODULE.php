<?php
	require_once 'orglist_functions.php';
	require_once 'IsOnlineController.class.php';
	require_once 'OrgMembersController.class.php';
	require_once 'FindOrgController.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'IsOnlineController', new IsOnlineController());
	$chatBot->registerInstance($MODULE_NAME, 'OrgMembersController', new OrgMembersController());
	$chatBot->registerInstance($MODULE_NAME, 'FindOrgController', new FindOrgController());

	$command->register($MODULE_NAME, "", "whoisorg.php", "whoisorg", "all", "Display org info", "whoisorg.txt");
	$command->register($MODULE_NAME, "", "orglist.php", "orglist", "guild", "Check someones org roster", "orglist.txt");

	$event->register($MODULE_NAME, "logOn", "orglist_check.php", "Gets online status of org members");
	$event->register($MODULE_NAME, "logOff", "orglist_check.php", "Gets offline status of org members");
?>

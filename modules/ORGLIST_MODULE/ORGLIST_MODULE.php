<?php
	require_once 'orglist_functions.php';
	
	$command->register($MODULE_NAME, "", "orgmembers.php", "orgmembers", "rl", "Show guild members sorted by name");
	$command->register($MODULE_NAME, "", "orgranks.php", "orgranks", "rl", "Show guild members sorted by guild rank");
	$command->register($MODULE_NAME, "", "whoisorg.php", "whoisorg", "all", "Display org info");
	$command->register($MODULE_NAME, "", "findorg.php", "findorg", "all", "Find orgs by name");
	$command->register($MODULE_NAME, "", "is_online.php", "is", "all", "Checks if a player is online");
	$command->register($MODULE_NAME, "", "orglist.php", "orglist", "guild", "Check someones org roster");

	Event::register($MODULE_NAME, "logOn", "orglist_check.php", "Gets online status of org members");
	Event::register($MODULE_NAME, "logOff", "orglist_check.php", "Gets offline status of org members");
	Event::register($MODULE_NAME, "logOn", "is_online_check.php", "Gets online status of player");
	Event::register($MODULE_NAME, "logOff", "is_online_check.php", "Gets offline status of player");

	Help::register($MODULE_NAME, "orglist", "orglist.txt", "guild", "See who is online from someones org");
	Help::register($MODULE_NAME, "findorg", "findorg.txt", "all", "Find an org by name");
	Help::register($MODULE_NAME, "is", "isonline.txt", "guild", "Checking if a player is online");
	Help::register($MODULE_NAME, "orgmembers", "orgmembers.txt", "guild", "How to use orgmembers");
	Help::register($MODULE_NAME, "orgranks", "orgranks.txt", "guild", "How to use orgranks");
	Help::register($MODULE_NAME, "whoisorg", "whoisorg.txt", "guild", "How to see basic info about an org");
?>

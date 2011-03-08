<?php
	$MODULE_NAME = "ORGLIST_MODULE";
 
	// Checks who in an org is online
	Command::register($MODULE_NAME, "", "orglist.php", "orglist", "guildadmin", "Check someones org roster");
	CommandAlias::register($MODULE_NAME, "orglist", "onlineorg");
	
	Command::register($MODULE_NAME, "", "orgmembers.php", "orgmembers", "guildadmin", "Show guild members sorted by name");
	Command::register($MODULE_NAME, "", "orgranks.php", "orgranks", "guildadmin", "Show guild members sorted by guild rank");

	Event::register($MODULE_NAME, "logOn", "orglist.php", "none", "Gets online status of org members");
	Event::register($MODULE_NAME, "logOff", "orglist.php", "none", "Gets offline status of org members");
	
	// Checks if a player is online
	Command::register($MODULE_NAME, "", "is_online.php", "is", "all", "Checks if a player is online");

	Event::register($MODULE_NAME, "logOn", "is_online.php", "none", "Gets online status of player");
	Event::register($MODULE_NAME, "logOff", "is_online.php", "none", "Gets offline status of player");

	// Help files
	Help::register($MODULE_NAME, "orglist", "orglist.txt", "all", "See who is online from someones org.");
	Help::register($MODULE_NAME, "is", "isonline.txt", "guild", "Checking if a player is online");
	Help::register($MODULE_NAME, "orgmembers", "orgmembers.txt", "guild", "How to use orgmembers");
	Help::register($MODULE_NAME, "orgranks", "orgranks.txt", "guild", "How to use orgranks");
?>

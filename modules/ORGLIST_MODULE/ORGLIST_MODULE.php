<?php
	$MODULE_NAME = "ORGLIST_MODULE";
 
	// Checks who in an org is online
	bot::command("", "$MODULE_NAME/orglist.php", "orglist", MODERATOR, "Check someones org roster");
	bot::command("", "$MODULE_NAME/orglist.php", "onlineorg", MODERATOR, "Check someones org roster");
	bot::event("logOn", "$MODULE_NAME/orglist.php", "orglist");
	bot::event("logOff", "$MODULE_NAME/orglist.php", "orglist");
	
	// Checks if a player is online
	bot::command("", "$MODULE_NAME/is_online.php", "is", ALL, "Checks if a player is online");
	bot::event("logOn", "$MODULE_NAME/is_online.php", "is");
	bot::event("logOff", "$MODULE_NAME/is_online.php", "is");

	// Help files
	bot::help("orglist", "$MODULE_NAME/orglist.txt", MODERATOR, "See who is online from someones org.");
	bot::help("IsOnline", "$MODULE_NAME/isonline.txt", ALL, "Checking if a player is online");
?>

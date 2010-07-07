<?php
	$MODULE_NAME = "ORGLIST_MODULE";
 
	// Checks who in an org is online
	bot::command("", "$MODULE_NAME/orglist.php", "orglist", "mod", "Check someones org roster");
	bot::command("", "$MODULE_NAME/orglist.php", "onlineorg", "mod", "Check someones org roster");
	bot::event("logOn", "$MODULE_NAME/orglist.php", "orglist");
	bot::event("logOff", "$MODULE_NAME/orglist.php", "orglist");
	
	// Checks if a player is online
	bot::command("", "$MODULE_NAME/is_online.php", "is", "all", "Checks if a player is online");
	bot::event("logOn", "$MODULE_NAME/is_online.php", "is");
	bot::event("logOff", "$MODULE_NAME/is_online.php", "is");

	// Helpfiles
	bot::help("orglist", "$MODULE_NAME/orglist.txt", "all", "See who is online from someones org.", "Orglist");
	bot::help("IsOnline", "$MODULE_NAME/isonline.txt", "guild", "Checking if a player is online", "Basic Guild Commands");
?>

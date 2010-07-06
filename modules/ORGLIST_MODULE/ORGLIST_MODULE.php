<?php
	$MODULE_NAME = "ORGLIST_MODULE";
 
	// Checks who in an org is online
	$this->command("", "$MODULE_NAME/orglist.php", "orglist", MODERATOR, "Check someones org roster");
	$this->command("", "$MODULE_NAME/orglist.php", "onlineorg", MODERATOR, "Check someones org roster");
	$this->event("logOn", "$MODULE_NAME/orglist.php", "orglist");
	$this->event("logOff", "$MODULE_NAME/orglist.php", "orglist");
	
	// Checks if a player is online
	$this->command("", "$MODULE_NAME/is_online.php", "is", ALL, "Checks if a player is online");
	$this->event("logOn", "$MODULE_NAME/is_online.php", "is");
	$this->event("logOff", "$MODULE_NAME/is_online.php", "is");

	// Help files
	$this->help("orglist", "$MODULE_NAME/orglist.txt", MODERATOR, "See who is online from someones org.");
	$this->help("IsOnline", "$MODULE_NAME/isonline.txt", ALL, "Checking if a player is online");
?>

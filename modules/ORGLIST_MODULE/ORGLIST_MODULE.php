<?php
	$MODULE_NAME = "ORGLIST_MODULE";
 
	bot::command("guild", "$MODULE_NAME/orglist.php", "orglist", "mod", "Check someones org roster");
	bot::command("guild", "$MODULE_NAME/orglist.php", "onlineorg", "mod", "Check someones org roster");

	bot::event("logOn", "$MODULE_NAME/orglist.php", "orglist");
	bot::event("logOff", "$MODULE_NAME/orglist.php", "orglist");

	bot::help("orglist", "$MODULE_NAME/orglist.txt", "all", "See who is online from someones org.", "Orglist");		//Helpfiles
?>

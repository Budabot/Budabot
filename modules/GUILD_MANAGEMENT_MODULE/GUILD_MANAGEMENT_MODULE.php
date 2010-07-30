<?php
$MODULE_NAME = "GUILD_MANAGEMENT_MODULE";

	//Check Inactives
	bot::command("", "$MODULE_NAME/inactive_mem.php", "inactivemem", "admin", "Check for inactive members");

	//Helpfiles
    bot::help("inactivemem", "$MODULE_NAME/manage_guild.txt", "all", "Help on Checking for Inactive Members", "Inactive Org Members");
 
?>

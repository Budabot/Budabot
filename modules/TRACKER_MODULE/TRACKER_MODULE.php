<?php
	$MODULE_NAME = "TRACKER_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "tracked_users");
	bot::loadSQLFile($MODULE_NAME, "tracking");
    
	bot::command("", "$MODULE_NAME/track.php", "track", "mod", "Lists all users on tracking list");
	
	bot::subcommand("", "$MODULE_NAME/add.php", "track add (.+)", "mod", "topic", "Adds a user to tracking list");
	bot::subcommand("", "$MODULE_NAME/rem.php", "track rem (.+)", "mod", "topic", "Removes a user to tracking list");
	
	bot::subcommand("", "$MODULE_NAME/track.php", "track (.+)", "mod", "topic", "Shows tracking history for a user");

	bot::event("logOn", "$MODULE_NAME/logoff.php", "none", "Records when a tracked user logs on");
	bot::event("logOff", "$MODULE_NAME/logon.php", "none", "Records when a tracked user logs off");
?>
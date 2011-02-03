<?php
	$MODULE_NAME = "TRACKER_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "tracked_users");
	bot::loadSQLFile($MODULE_NAME, "tracking");
    
	bot::command("", "$MODULE_NAME/track.php", "track", "mod", "Lists all users on tracking list");
	
	Event::register($MODULE_NAME, "logOn", "logon.php", "none", "Records when a tracked user logs on");
	Event::register($MODULE_NAME, "logOff", "logoff.php", "none", "Records when a tracked user logs off");
?>
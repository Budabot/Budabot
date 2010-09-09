<?php
	$MODULE_NAME = "TRACKER_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "tracked_users");
	bot::loadSQLFile($MODULE_NAME, "tracking");
    
	bot::command("", "$MODULE_NAME/track.php", "track", "mod", "Lists all users on tracking list");
	
	bot::event("logOn", "$MODULE_NAME/logon.php", "none", "Records when a tracked user logs on");
	bot::event("logOff", "$MODULE_NAME/logoff.php", "none", "Records when a tracked user logs off");
?>
<?php
	$MODULE_NAME = "TRACKER_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "tracked_users");
	DB::loadSQLFile($MODULE_NAME, "tracking");
    
	Command::register($MODULE_NAME, "", "track.php", "track", "guild", "Lists all users on tracking list");
	
	Event::register($MODULE_NAME, "logOn", "logon.php", "none", "Records when a tracked user logs on");
	Event::register($MODULE_NAME, "logOff", "logoff.php", "none", "Records when a tracked user logs off");
	
	Help::register($MODULE_NAME, "track", "track.txt", "guild", "How to track players");
?>
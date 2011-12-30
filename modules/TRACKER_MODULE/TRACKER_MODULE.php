<?php
	Event::register($MODULE_NAME, "connect", "setup.php", "Adds all players on the track list to the bot's friendlist");
	
	$db->loadSQLFile($MODULE_NAME, "tracked_users");
	$db->loadSQLFile($MODULE_NAME, "tracking");
    
	Command::register($MODULE_NAME, "", "track.php", "track", "guild", "Lists all users on tracking list");
	
	Event::register($MODULE_NAME, "logOn", "logon.php", "Records when a tracked user logs on");
	Event::register($MODULE_NAME, "logOff", "logoff.php", "Records when a tracked user logs off");
	
	Help::register($MODULE_NAME, "track", "track.txt", "guild", "How to track players");
?>
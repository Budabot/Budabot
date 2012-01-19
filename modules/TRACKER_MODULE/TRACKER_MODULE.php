<?php
	$event->register($MODULE_NAME, "connect", "setup.php", "Adds all players on the track list to the bot's friendlist");
	
	$db->loadSQLFile($MODULE_NAME, "tracked_users");
	$db->loadSQLFile($MODULE_NAME, "tracking");
    
	$command->register($MODULE_NAME, "", "track.php", "track", "guild", "Lists all users on tracking list", "track.txt");
	
	$event->register($MODULE_NAME, "logOn", "logon.php", "Records when a tracked user logs on");
	$event->register($MODULE_NAME, "logOff", "logoff.php", "Records when a tracked user logs off");
?>
<?php
	DB::loadSQLFile($MODULE_NAME, 'org_city');

    Command::register($MODULE_NAME, "", "cloak.php", "cloak", "guild", "Shows the status of the city cloak");
	CommandAlias::register($MODULE_NAME, "cloak", "city");

	Event::register($MODULE_NAME, "guild", "record_cloak_changes.php", "city", "Records when the cloak is raised or lowered");
    Event::register($MODULE_NAME, "1min", "city_guild_timer.php", "city", "Checks timer to see if cloak can be raised or lowered");
	Event::register($MODULE_NAME, "1min", "cloak_reminder.php", "city", "Reminds the player who lowered cloak to raise it");
	Event::register($MODULE_NAME, "logOn", "city_guild_logon.php", "city", "Displays summary of city status.");
	
	Setting::add($MODULE_NAME, "showcloakstatus", "Show cloak status to players at logon", "edit", "options", "1", "Never;When cloak is down;Always", "0;1;2");
	
	// Auto Wave
	Command::register($MODULE_NAME, "", "start.php", "startraid", "guild", "manually starts wave counter", "wavecounter");
	Command::register($MODULE_NAME, "", "stopraid.php", "stopraid", "guild", "manually stops wave counter", "wavecounter");
	Command::register($MODULE_NAME, "", "citywave.php", "citywave", "guild", "Shows the current city wave", "wavecounter");
	Event::register($MODULE_NAME, "guild", "start.php", "none", "Starts a wave counter when cloak is lowered");
	Event::register($MODULE_NAME, "2sec", "counter.php", "none", "Checks timer to see when next wave should come");
	
	// OS/AS timer
	Event::register($MODULE_NAME, "orgmsg", "os_timer.php", "none", "Sets a timer when an OS/AS is launched");
	
	// Help files
	Help::register($MODULE_NAME, "cloak", "cloak.txt", "guild", "Status of the city cloak");
	Help::register($MODULE_NAME, "wavecounter", "wavecounter.txt", "guild", "How to manually start and stop the wave counter");
?>
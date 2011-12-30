<?php
	$db->loadSQLFile($MODULE_NAME, 'org_city');

    $command->register($MODULE_NAME, "", "cloak.php", "cloak", "guild", "Shows the status of the city cloak");
	CommandAlias::register($MODULE_NAME, "cloak", "city");

	$event->register($MODULE_NAME, "guild", "record_cloak_changes.php", "Records when the cloak is raised or lowered");
    $event->register($MODULE_NAME, "1min", "city_guild_timer.php", "Checks timer to see if cloak can be raised or lowered");
	$event->register($MODULE_NAME, "1min", "cloak_reminder.php", "Reminds the player who lowered cloak to raise it");
	$event->register($MODULE_NAME, "logOn", "city_guild_logon.php", "Show cloak status to guild members logging in");
	
	Setting::add($MODULE_NAME, "showcloakstatus", "Show cloak status to players at logon", "edit", "options", "1", "Never;When cloak is down;Always", "0;1;2");
	Setting::add($MODULE_NAME, "cloak_reminder_interval", "How often to spam guild channel when cloak is down", "edit", "time", "5m", "2m;5m;10m;15m;20m");
	
	// Auto Wave
	$command->register($MODULE_NAME, "", "startraid.php", "startraid", "guild", "manually starts wave counter", "wavecounter");
	$command->register($MODULE_NAME, "", "stopraid.php", "stopraid", "guild", "manually stops wave counter", "wavecounter");
	$command->register($MODULE_NAME, "", "citywave.php", "citywave", "guild", "Shows the current city wave", "wavecounter");
	$event->register($MODULE_NAME, "guild", "auto_start_wave_counter.php", "Starts a wave counter when cloak is lowered");
	$event->register($MODULE_NAME, "2sec", "counter.php", "Checks timer to see when next wave should come");
	
	// OS/AS timer
	$event->register($MODULE_NAME, "orgmsg", "os_timer.php", "Sets a timer when an OS/AS is launched");
	
	// Help files
	Help::register($MODULE_NAME, "cloak", "cloak.txt", "guild", "How to see the status of the city cloak");
	Help::register($MODULE_NAME, "wavecounter", "wavecounter.txt", "guild", "How to manually start and stop the wave counter");
?>
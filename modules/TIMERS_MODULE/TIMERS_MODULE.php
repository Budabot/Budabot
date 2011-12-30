<?php
	require_once 'Timer.class.php';

	$db->loadSQLFile($MODULE_NAME, 'timers');

	$event->register($MODULE_NAME, "setup", "setup.php");

	$command->register($MODULE_NAME, "", "rtimer.php", "rtimer", "guild", "Add a repeating timer", 'timers');
	$command->register($MODULE_NAME, "", "timers.php", "timers", "guild", "Set timers/Show running timers");
	
	$command->register($MODULE_NAME, "", "countdown.php", "countdown", "guild", "Set a countdown");
	CommandAlias::register($MODULE_NAME, "countdown", "cd");

	$event->register($MODULE_NAME, "2sec", "timers_check.php", "Checks timers and periodically updates chat with time left");

	Help::register($MODULE_NAME, "timers", "timers.txt", "guild", "How to create and show timers");
	Help::register($MODULE_NAME, "countdown", "countdown.txt", "guild", "How to create a 5 second countdown timer");
?>
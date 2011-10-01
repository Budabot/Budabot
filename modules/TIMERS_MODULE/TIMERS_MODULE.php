<?php
	require_once 'Timer.class.php';

	Event::register($MODULE_NAME, "setup", "setup.php");

	// Timer Module
	Command::register($MODULE_NAME, "", "timers.php", "timers", "guild", "Set timers/Show running Timers");
	CommandAlias::register($MODULE_NAME, "timers", "timer");
	
	Command::register($MODULE_NAME, "", "countdown.php", "countdown", "guild", "Set a countdown");
	CommandAlias::register($MODULE_NAME, "countdown", "cd");

	Event::register($MODULE_NAME, "2sec", "timers_check.php", "timer", "Checks timers and periodically updates chat with time left");
	
	Setting::add($MODULE_NAME, "timers_window", "Show running timers in a window or directly", "edit", "options", "1", "window only;chat only;window after 3;window after 4;window after 5", '1;2;3;4;5', "mod");

	//Help files
	Help::register($MODULE_NAME, "timers", "timers.txt", "guild", "How to create and show timers");
	Help::register($MODULE_NAME, "countdown", "countdown.txt", "guild", "How to create a 5 second countdown timer");
?>
<?php
	require_once 'Timer.class.php';

	$MODULE_NAME = "TIMERS_MODULE";

	Event::register($MODULE_NAME, "setup", "setup.php");

	// Timer Module
	Command::register($MODULE_NAME, "", "timers.php", "timers", "all", "Set timers/Show running Timers");
	CommandAlias::register($MODULE_NAME, "timers", "timer");
	Command::register($MODULE_NAME, "", "countdown.php", "countdown", "all", "Set a countdown");
	Command::register($MODULE_NAME, "", "countdown.php", "cd", "all", "Set a countdown");

	Event::register($MODULE_NAME, "2sec", "timers_check.php", "timer", "Checks timers and periodically updates chat with time left");
	
	Setting::add($MODULE_NAME, "timers_window", "Show running timers in a window or directly", "edit", "options", "1", "window only;chat only;window after 3;window after 4;window after 5", '1;2;3;4;5', "mod");

	//Help files
	Help::register($MODULE_NAME, "timer", "timer.txt", "guild", "Set/Show Timers");
?>
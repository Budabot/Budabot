<?php
	$MODULE_NAME = "TIMERS_MODULE";

	bot::event("setup", "$MODULE_NAME/setup.php");

	// Timer Module
	bot::command("", "$MODULE_NAME/timers.php", "timer", "all", "Set Personal Timers");
	bot::command("", "$MODULE_NAME/timers.php", "timers", "all", "Shows running Timers");
	bot::command("", "$MODULE_NAME/countdown.php", "countdown", "all", "Set a countdown");
	bot::command("", "$MODULE_NAME/countdown.php", "cd", "all", "Set a countdown");
	bot::regGroup("timers", $MODULE_NAME, "Set/Show Personal Timer", "timer", "timers");

	bot::event("2sec", "$MODULE_NAME/timers_check.php", "timer", "Checks timers and periodically updates chat with time left");
	
	bot::addsetting($MODULE_NAME, "timers_window", "Show running timers in a window or directly", "edit", "1", "window only;chat only;window after 3;window after 4;window after 5", '1;2;3;4;5', "mod");

	//Help files
	bot::help($MODULE_NAME, "timer", "timer.txt", "guild", "Set/Show Timers");
?>
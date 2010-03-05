<?
$MODULE_NAME = "TIMERS_MODULE";
$PLUGIN_VERSION = 0.1;

	bot::event("setup", "$MODULE_NAME/setup.php");

	// Timer Module
	bot::command("guild", "$MODULE_NAME/timers.php", "timer", "all", "Set Personal Timers");
	bot::command("msg", "$MODULE_NAME/timers.php", "timer", "all", "Set Personal Timers");
	bot::command("priv", "$MODULE_NAME/timers.php", "timer", "all", "Set Personal Timers");
	bot::command("guild", "$MODULE_NAME/timers.php", "timers", "all", "Shows running Timers");
	bot::command("msg", "$MODULE_NAME/timers.php", "timers", "all", "Shows running Timers");
	bot::command("priv", "$MODULE_NAME/timers.php", "timers", "all", "Shows running Timers");
	bot::event("2sec", "$MODULE_NAME/timers_check.php", "timer");
	bot::regGroup("timers", $MODULE_NAME, "Set/Show Personal Timer", "timer", "timers");
	
	bot::addsetting("timers_window", "Show running timers in a window or directly", "edit", "1", "window only;chat only;window after 3;window after 4;window after 5", '1;2;3;4;5', "mod");

	//Countdown	
	bot::command("guild", "$MODULE_NAME/countdown.php", "countdown", "all", "Set a countdown");
	bot::command("msg", "$MODULE_NAME/countdown.php", "countdown", "all", "Set a countdown");
	bot::command("priv", "$MODULE_NAME/countdown.php", "countdown", "all", "Set a countdown");
	
	//Help files
	bot::help("Timer", "$MODULE_NAME/timer.txt", "guild", "Set/Show Timers.", "Timers");
?>
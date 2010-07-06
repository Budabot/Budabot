<?php
	$MODULE_NAME = "TIMERS_MODULE";
	$PLUGIN_VERSION = 0.1;

	$this->event("setup", "$MODULE_NAME/setup.php");

	// Timer Module
	$this->command("", "$MODULE_NAME/timers.php", "timer", GUILDMEMBER, "Set Personal Timers");
	$this->command("", "$MODULE_NAME/timers.php", "timers", GUILDMEMBER, "Shows running Timers");
	$this->command("", "$MODULE_NAME/countdown.php", "countdown", GUILDMEMBER, "Set a countdown");

	$this->event("2sec", "$MODULE_NAME/timers_check.php", "timer");
	$this->regGroup("timers", $MODULE_NAME, "Set/Show Personal Timer", "timer", "timers");
	
	$this->addsetting("timers_window", "Show running timers in a window or directly", "edit", "1", "window only;chat only;window after 3;window after 4;window after 5", '1;2;3;4;5', MODERATOR);

	//Help files
	$this->help("Timer", "$MODULE_NAME/timer.txt", GUILDMEMBER, "Set/Show Timers.");
?>
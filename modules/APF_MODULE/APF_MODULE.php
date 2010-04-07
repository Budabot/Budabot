<?
	$MODULE_NAME = "APF_MODULE";

	//Loottable for the different APF Sectors
	bot::command("", "$MODULE_NAME/loottable.php", "loottable", "all", "Shows what drops of APF Boss");

	//Guides for the different APF items
	bot::command("", "$MODULE_NAME/tradeskill_loot.php", "guide", "all", "Shows what to make from apf items");

	//Shows opening times of APF
	bot::command("", "$MODULE_NAME/show_pftimer.php", "pf", "all", "Shows the opening time of APF");
	bot::event("2sec", "$MODULE_NAME/check_pftimer.php", "pf");

	//Shows time for next ship to APF
	bot::command("", "$MODULE_NAME/show_shiptimer.php", "ship", "all", "Shows the time till next ship arrives to APF");

	//Adjust timers manually
	bot::command("msg", "$MODULE_NAME/set_pftimer.php", "setpf", "all", "Sets the opening time of APF");
	bot::command("msg", "$MODULE_NAME/set_shiptimer.php", "setship", "all", "Sets the ship timer to APF");	

	//Update Timers automatically over a timer bot
	bot::event("msg", "$MODULE_NAME/autoupdate_timers.php", "none", "Enable autoupdates of timers over timerbot");
	
	//Settings for this module	
	bot::addsetting("pftimer", "no", "hide", time());
	bot::addsetting("pftimer_status", "no", "hide", "no_correction");
	bot::addsetting("shiptimer", "no", "hide", time());
	bot::addsetting("shiptimer_status", "no", "hide", "no_correction");
	bot::addsetting("apftimerbot", "Bot for autoupdating APF Timers", "edit", "Not set yet", "text");
	
	//Helpfiles
	bot::help("apf_timer", "$MODULE_NAME/apftimer.txt", "guild", "Set/View the APF Timers", "Alien Playfield");
	bot::help("apf_loot", "$MODULE_NAME/apfloot.txt", "guild", "Show the Loots of the APF", "Alien Playfield");
	bot::help("apf_autotimer", "$MODULE_NAME/apfautotimer.txt", "guild", "Autoupdate of the APF Timers", "Alien Playfield");
?>
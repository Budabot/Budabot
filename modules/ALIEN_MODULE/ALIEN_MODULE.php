<?php
	$MODULE_NAME = "ALIEN_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "leprocs");

	Command::register($MODULE_NAME, "", "bio.php", "bio", "all", "Biomaterial Identify");
	Command::register($MODULE_NAME, "", "aigen.php", "aigen", "all", "Info about Alien City Generals(which VBs they drop)");
	Command::register($MODULE_NAME, "", "aiarmor.php", "aiarmor", "all", "Tradeskillprocess for Alien Armor");

	Command::register($MODULE_NAME, "", "leprocs.php", "leprocs", "all", "Shows the LE Procs for a particular profession");
	CommandAlias::register($MODULE_NAME, "leprocs", "leproc");

	// Help files
    Help::register($MODULE_NAME, "bio", "bio.txt", "all", "Identify an Biomaterial");
    Help::register($MODULE_NAME, "aiarmor", "aiarmor.txt", "all", "Alien armor Tradeskillprocess");
	Help::register($MODULE_NAME, "aigen", "aigen.txt", "all", "Alien City Generals Info");
	Help::register($MODULE_NAME, "leprocs", "leprocs.txt", "all", "How to use leprocs");
?>

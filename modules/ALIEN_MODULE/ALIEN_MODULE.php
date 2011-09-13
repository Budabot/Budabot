<?php
	$MODULE_NAME = "ALIEN_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'clump');
	DB::loadSQLFile($MODULE_NAME, "leprocs");

	Command::register($MODULE_NAME, "", "ofab.php", "ofab", "all", "Show Ofab armor bio-material types");
	Command::register($MODULE_NAME, "", "bio.php", "bio", "all", "Identify Solid Clump of Kyr'Ozch Bio-Material");
	Command::register($MODULE_NAME, "", "aigen.php", "aigen", "all", "Info about Alien City Generals(which VBs they drop)");
	Command::register($MODULE_NAME, "", "aiarmor.php", "aiarmor", "all", "Tradeskill process for Alien Armor");
	Command::register($MODULE_NAME, "", "bioinfo.php", "bioinfo", "all", "Show info about a particular bio type");

	Command::register($MODULE_NAME, "", "leprocs.php", "leprocs", "all", "Shows the LE Procs for a particular profession");
	CommandAlias::register($MODULE_NAME, "leprocs", "leproc");

	// Help files
    Help::register($MODULE_NAME, "bio", "bio.txt", "all", "Identify an Biomaterial");
	Help::register($MODULE_NAME, "bioinfo", "bioinfo.txt", "all", "How to find info on a bio type");
    Help::register($MODULE_NAME, "aiarmor", "aiarmor.txt", "all", "Alien armor Tradeskillprocess");
	Help::register($MODULE_NAME, "aigen", "aigen.txt", "all", "Alien City Generals Info");
	Help::register($MODULE_NAME, "leprocs", "leprocs.txt", "all", "How to use leprocs");
	Help::register($MODULE_NAME, "ofab", "ofab.txt", "all", "How to use ofab");
?>

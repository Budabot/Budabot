<?php
	require_once 'functions.php';
	
	$db->loadSQLFile($MODULE_NAME, 'leprocs');
	$db->loadSQLFile($MODULE_NAME, 'ofabarmor');
	$db->loadSQLFile($MODULE_NAME, 'ofabweapons');
	$db->loadSQLFile($MODULE_NAME, 'alienweapons');

	$command->register($MODULE_NAME, "", "ofabarmor.php", "ofabarmor", "all", "Show Ofab armor and VP cost");
	$command->register($MODULE_NAME, "", "ofabweapons.php", "ofabweapons", "all", "Show Ofab weapons and VP cost");
	$command->register($MODULE_NAME, "", "bio.php", "bio", "all", "Identify Solid Clump of Kyr'Ozch Bio-Material");
	$command->register($MODULE_NAME, "", "aigen.php", "aigen", "all", "Info about Alien City Generals");
	$command->register($MODULE_NAME, "", "aiarmor.php", "aiarmor", "all", "Tradeskill process for Alien Armor");
	$command->register($MODULE_NAME, "", "bioinfo.php", "bioinfo", "all", "Show info about a particular bio type");
	CommandAlias::register($MODULE_NAME, "bioinfo", "biotype");

	$command->register($MODULE_NAME, "", "leprocs.php", "leprocs", "all", "Shows the LE Procs for a particular profession");

    Help::register($MODULE_NAME, "bio", "bio.txt", "all", "Identify an Biomaterial");
	Help::register($MODULE_NAME, "bioinfo", "bioinfo.txt", "all", "How to find info on a bio type");
    Help::register($MODULE_NAME, "aiarmor", "aiarmor.txt", "all", "Alien armor Tradeskillprocess");
	Help::register($MODULE_NAME, "aigen", "aigen.txt", "all", "Alien City Generals Info");
	Help::register($MODULE_NAME, "leprocs", "leprocs.txt", "all", "How to use leprocs");
	Help::register($MODULE_NAME, "ofabarmor", "ofabarmor.txt", "all", "How to use ofabarmor");
	Help::register($MODULE_NAME, "ofabweapons", "ofabweapons.txt", "all", "How to use ofabweapons");
?>

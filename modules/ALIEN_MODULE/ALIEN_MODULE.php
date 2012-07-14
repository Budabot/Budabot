<?php
	require_once 'functions.php';

	$db->loadSQLFile($MODULE_NAME, 'leprocs');
	$db->loadSQLFile($MODULE_NAME, 'ofabarmor');
	$db->loadSQLFile($MODULE_NAME, 'ofabweapons');
	$db->loadSQLFile($MODULE_NAME, 'alienweapons');

	$command->register($MODULE_NAME, "", "leprocs.php", "leprocs", "all", "Shows the LE Procs for a particular profession", "leprocs.txt");
	$command->register($MODULE_NAME, "", "ofabarmor.php", "ofabarmor", "all", "Show Ofab armor and VP cost", "ofabarmor.txt");
	$command->register($MODULE_NAME, "", "ofabweapons.php", "ofabweapons", "all", "Show Ofab weapons and VP cost", "ofabweapons.txt");
	$command->register($MODULE_NAME, "", "bio.php", "bio", "all", "Identify Solid Clump of Kyr'Ozch Bio-Material", "bio.txt");
	$command->register($MODULE_NAME, "", "aigen.php", "aigen", "all", "Info about Alien City Generals", "aigen.txt");
	$command->register($MODULE_NAME, "", "aiarmor.php", "aiarmor", "all", "Tradeskill process for Alien Armor", "aiarmor.txt");
	$command->register($MODULE_NAME, "", "bioinfo.php", "bioinfo", "all", "Show info about a particular bio type", "bioinfo.txt");
	$commandAlias->register($MODULE_NAME, "bioinfo", "biotype");
?>

<?php
	//Search for Database Updates
	$db->loadSQLFile($MODULE_NAME, "nanos");
	$db->loadSQLFile($MODULE_NAME, "nanolines");
	$db->loadSQLFile($MODULE_NAME, "nanolines_ref");

	//nano Search
	$command->register($MODULE_NAME, "", "nano.php", "nano", "all", "Searches for a nano and tells you were to get it", "nano.txt");
	$command->register($MODULE_NAME, "", "nanoloc.php", "nanoloc", "all", "Browse nanos by location", "nano.txt");
	$command->register($MODULE_NAME, "", "fp.php", "fp", "all", "Shows whether or not a nano is usable in false profession", "fp.txt");
	$command->register($MODULE_NAME, "", "nanolines.php", "nanolines", "all", "Shows nanos based on nanoline", "nanolines.txt");

	//Settings
	$setting->add($MODULE_NAME, 'maxnano', 'Number of Nanos shown on the list', 'edit', "number", '40', '30;40;50;60', "", "mod");
	$setting->add($MODULE_NAME, "shownanolineicons", "Show icons for the nanolines", "edit", "options", "0", "true;false", "1;0");
?>
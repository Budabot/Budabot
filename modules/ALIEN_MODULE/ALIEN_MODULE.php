<?php
	$MODULE_NAME = "ALIEN_MODULE";

	Command::register($MODULE_NAME, "", "bio.php", "bio", "all", "Biomaterial Identify");
	Command::register($MODULE_NAME, "", "aigen.php", "aigen", "all", "Info about Alien City Generals(which VBs they drop)");
	Command::register($MODULE_NAME, "", "aiarmor.php", "aiarmor", "all", "Tradeskillprocess for Alien Armor");

	// Help files
    Help::register($MODULE_NAME, "bio", "bio.txt", "all", "Identify an Biomaterial");
    Help::register($MODULE_NAME, "aiarmor", "aiarmor.txt", "all", "Alien armor Tradeskillprocess");
	Help::register($MODULE_NAME, "aigen", "aigen.txt", "all", "Alien City Generals Info");
?>

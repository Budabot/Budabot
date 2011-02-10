<?php
	$MODULE_NAME = "APF_MODULE";

	//Loottable for the different APF Sectors
	Command::register($MODULE_NAME, "", "loottable.php", "loottable", "all", "Shows what drops of APF Boss");

	//Guides for the different APF items
	Command::register($MODULE_NAME, "", "apfloot.php", "apfloot", "all", "Shows what to make from apf items");

	//Help files
	Help::register($MODULE_NAME, "apfloot", "apfloot.txt", "guild", "Show the Loots of the APF");
?>
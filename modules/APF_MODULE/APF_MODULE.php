<?php
	$MODULE_NAME = "APF_MODULE";

	//Loottable for the different APF Sectors
	bot::command("", "$MODULE_NAME/loottable.php", "loottable", "all", "Shows what drops of APF Boss");

	//Guides for the different APF items
	bot::command("", "$MODULE_NAME/apfloot.php", "apfloot", "all", "Shows what to make from apf items");

	//Help files
	bot::help($MODULE_NAME, "apfloot", "apfloot.txt", "guild", "Show the Loots of the APF");
?>
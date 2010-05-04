<?php
	$MODULE_NAME = "APF_MODULE";

	//Loottable for the different APF Sectors
	bot::command("", "$MODULE_NAME/loottable.php", "loottable", "all", "Shows what drops of APF Boss");

	//Guides for the different APF items
	bot::command("", "$MODULE_NAME/tradeskill_loot.php", "guide", "all", "Shows what to make from apf items");

	//Helpfiles
	bot::help("apf_loot", "$MODULE_NAME/apfloot.txt", "guild", "Show the Loots of the APF", "Alien Playfield");
?>
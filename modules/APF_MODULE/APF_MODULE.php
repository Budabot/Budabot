<?php
	$MODULE_NAME = "APF_MODULE";

	//Loottable for the different APF Sectors
	$this->command("", "$MODULE_NAME/loottable.php", "loottable", ALL, "Shows what drops of APF Boss");

	//Guides for the different APF items
	$this->command("", "$MODULE_NAME/tradeskill_loot.php", "guide", ALL, "Shows what to make from apf items");

	//Help files
	$this->help("apf_loot", "$MODULE_NAME/apfloot.txt", GUILDMEMBER, "Show the Loots of the APF");
?>
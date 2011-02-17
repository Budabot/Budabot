<?php
	$MODULE_NAME = "RAID_MODULE";

	//Loot list and adding/removing of players	
	Command::register($MODULE_NAME, "", "loot.php", "loot", "leader", "Adds an item to the loot list");
	Command::register($MODULE_NAME, "", "multiloot.php", "multiloot", "leader", "Adds items using multiloot");
	Command::register($MODULE_NAME, "", "loot.php", "clear", "leader", "Clears the loot list");
	Command::register($MODULE_NAME, "", "rollloot.php", "flatroll", "leader", "Rolls the loot list");
	Command::register($MODULE_NAME, "", "rollloot.php", "rollloot", "leader", "Rolls the loot list");
	Command::register($MODULE_NAME, "", "rollloot.php", "result", "leader", "Rolls the loot list");
	Command::register($MODULE_NAME, "", "rollloot.php", "win", "leader", "Rolls the loot list");
	Command::register($MODULE_NAME, "", "remloot.php", "remloot", "leader", "Remove item from loot list");
	Command::register($MODULE_NAME, "", "reroll.php", "reroll", "leader", "Rerolls the residual loot list");
	Command::register($MODULE_NAME, "", "13.php", "13", "leader", "Adds apf13 loot list");
	Command::register($MODULE_NAME, "", "28.php", "28", "leader", "Adds apf28 loot list");
	Command::register($MODULE_NAME, "", "35.php", "35", "leader", "Adds apf35 loot list");
	
	/* Commands used for both methods */
	//Adding/Removing from loot
	Command::register($MODULE_NAME, "", "list.php", "list", "all", "Shows the loot list");
	Command::register($MODULE_NAME, "", "add.php", "add", "all", "Let a player adding to a slot");	
	Command::register($MODULE_NAME, "", "rem.php", "rem", "all", "Let a player removing from a slot");
	
	//Settings
	Setting::add($MODULE_NAME, "add_on_loot", "Adding to loot show on", "edit", "options", "1", "tells;privatechat;privatechat and tells", '1;2;3', "mod");

	//Help files
	Help::register($MODULE_NAME, "add", "add_rem.txt", "all", "Adding to a lootitem");
	Help::register($MODULE_NAME, "rem", "add_rem.txt", "all", "Removing your bid on a lootitem");
	Help::register($MODULE_NAME, "loot", "flatroll.txt", "leader", "Adding an item to be flatrolled");
	Help::register($MODULE_NAME, "remloot", "flatroll.txt", "leader", "Removing an item from a flatroll list");
	Help::register($MODULE_NAME, "flatroll", "flatroll.txt", "leader", "Flatroll an item");
	Help::register($MODULE_NAME, "multiloot", "flatroll.txt", "leader", "Adding multiple of an item to be rolled");

?>
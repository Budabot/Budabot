<?php
$MODULE_NAME = "RAID_MODULE";

	/* Commands used only for flatroll of items */
	//Set requirements for the loot roll		

	//Loot list and adding/removing of players	
	bot::command("", "$MODULE_NAME/loot.php", "loot", LEADER, "Adds an item to the loot list");
	bot::command("", "$MODULE_NAME/multiloot.php", "multiloot", LEADER, "Adds items using multiloot");
	bot::command("", "$MODULE_NAME/loot.php", "clear", LEADER, "Clears the loot list");
	bot::command("", "$MODULE_NAME/rollloot.php", "flatroll", LEADER, "Rolls the loot list");
	bot::command("", "$MODULE_NAME/rollloot.php", "rollloot", LEADER, "Rolls the loot list");
	bot::command("", "$MODULE_NAME/rollloot.php", "result", LEADER, "Rolls the loot list");
	bot::command("", "$MODULE_NAME/rollloot.php", "win", LEADER, "Rolls the loot list");
	bot::command("", "$MODULE_NAME/remloot.php", "remloot", LEADER, "Remove item from loot list");
	bot::command("", "$MODULE_NAME/reroll.php", "reroll", LEADER, "Rerolls the residual loot list");
	bot::command("", "$MODULE_NAME/13.php", "13", LEADER, "Adds apf13 loot list");
	bot::command("", "$MODULE_NAME/28.php", "28", LEADER, "Adds apf28 loot list");
	bot::command("", "$MODULE_NAME/35.php", "35", LEADER, "Adds apf35 loot list");
	
	/* Commands used for both methods */
	//Adding/Removing from loot
	bot::command("", "$MODULE_NAME/list.php", "list", ALL, "Shows the loot list");
	bot::command("", "$MODULE_NAME/add.php", "add", ALL, "Let a player adding to a slot");	
	bot::command("", "$MODULE_NAME/rem.php", "rem", ALL, "Let a player removing from a slot");
	
	//Settings
	bot::addsetting("add_on_loot", "Adding to loot show on", "edit", "1", "tells;privatechat;privatechat and tells", '1;2;3', MODERATOR);
	
	bot::regGroup("basic_loot", $MODULE_NAME, "Handles a basic flatrolled loot system", "loot", "add", "clear", "list", "flatroll");

	//Help files
	bot::help("add", "$MODULE_NAME/add_rem.txt", ALL, "Adding to a lootitem");
	bot::help("rem", "$MODULE_NAME/add_rem.txt", ALL, "Removing your bid on a lootitem");
	bot::help("loot", "$MODULE_NAME/flatroll.txt", LEADER, "Adding an item to be flatrolled");
	bot::help("remloot", "$MODULE_NAME/flatroll.txt", LEADER, "Removing an item from a flatroll list");
	bot::help("flatroll", "$MODULE_NAME/flatroll.txt", LEADER, "Flatroll an item");
	bot::help("multiloot", "$MODULE_NAME/flatroll.txt", LEADER, "Adding multiple of an item to be rolled");

?>
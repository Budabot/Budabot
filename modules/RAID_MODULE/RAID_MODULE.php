<?
$MODULE_NAME = "RAID_MODULE";

	/* Commands used only for flatroll of items */
	//Set requirements for the loot roll		

	//Loot list and adding/removing of players	
	bot::command("msg", "$MODULE_NAME/loot.php", "loot", "leader", "Adds an item to the loot list");
	bot::command("msg", "$MODULE_NAME/multiloot.php", "multiloot", "leader", "Adds items using multiloot");
	bot::command("msg", "$MODULE_NAME/loot.php", "clear", "leader", "Clears the loot list");
	bot::command("msg", "$MODULE_NAME/rollloot.php", "flatroll", "leader", "Rolls the loot list");
	bot::command("msg", "$MODULE_NAME/rollloot.php", "roll", "leader", "Rolls the loot list");
	bot::command("msg", "$MODULE_NAME/rollloot.php", "result", "leader", "Rolls the loot list");
	bot::command("msg", "$MODULE_NAME/rollloot.php", "win", "leader", "Rolls the loot list");
	bot::command("msg", "$MODULE_NAME/remloot.php", "remloot", "leader", "Remove item from loot list");
	bot::command("msg", "$MODULE_NAME/reroll.php", "reroll", "leader", "Rerolls the residual loot list");
	bot::command("msg", "$MODULE_NAME/13.php", "13", "leader", "Adds apf13 loot list");
	bot::command("msg", "$MODULE_NAME/28.php", "28", "leader", "Adds apf28 loot list");
	bot::command("msg", "$MODULE_NAME/35.php", "35", "leader", "Adds apf35 loot list");
	
	bot::command("priv", "$MODULE_NAME/loot.php", "loot", "leader", "Adds an item to the loot list");
	bot::command("priv", "$MODULE_NAME/multiloot.php", "multiloot", "leader", "Adds items using multiloot");
	bot::command("priv", "$MODULE_NAME/loot.php", "clear", "leader", "Clears the loot list");
	bot::command("priv", "$MODULE_NAME/rollloot.php", "flatroll", "leader", "Rolls the loot list");
	bot::command("priv", "$MODULE_NAME/rollloot.php", "roll", "leader", "Rolls the loot list");
	bot::command("priv", "$MODULE_NAME/rollloot.php", "result", "leader", "Rolls the loot list");
	bot::command("priv", "$MODULE_NAME/rollloot.php", "win", "leader", "Rolls the loot list");
	bot::command("priv", "$MODULE_NAME/remloot.php", "remloot", "leader", "Remove item from loot list");
	bot::command("priv", "$MODULE_NAME/reroll.php", "reroll", "leader", "Rerolls the residual loot list");
	bot::command("priv", "$MODULE_NAME/13.php", "13", "leader", "Adds apf13 loot list");
	bot::command("priv", "$MODULE_NAME/28.php", "28", "leader", "Adds apf28 loot list");
	bot::command("priv", "$MODULE_NAME/35.php", "35", "leader", "Adds apf35 loot list");

	//Settings
	bot::addsetting("add_on_loot", "Adding to loot show on", "edit", "1", "tells;privatechat;privatechat and tells", '1;2;3', "mod");

	/* Commands used for both methods */
	//Adding/Removing from loot
	bot::command("priv", "$MODULE_NAME/list.php", "list", "all", "Shows the loot list");
	bot::command("msg", "$MODULE_NAME/list.php", "list", "all", "Shows the loot list");
	bot::command("priv", "$MODULE_NAME/add.php", "add", "all", "Let a player adding to a slot");	
	bot::command("msg", "$MODULE_NAME/add.php", "add", "all", "Let a player adding to a slot");
	bot::command("priv", "$MODULE_NAME/rem.php", "rem", "all", "Let a player removing from a slot");
	bot::command("msg", "$MODULE_NAME/rem.php", "rem", "all", "Let a player removing from a slot");
	
	bot::regGroup("basic_loot", $MODULE_NAME, "Handles a basic flatrolled loot system", "loot", "add", "clear", "list", "flatroll");

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Helpfiles
	bot::help("add", "$MODULE_NAME/add_rem.txt", "all", "Adding to a lootitem", "Raid");
	bot::help("rem", "$MODULE_NAME/add_rem.txt", "all", "Removing your bid on a lootitem", "Raid");
	bot::help("loot", "$MODULE_NAME/flatroll.txt", "leader", "Adding an item to be flatrolled", "Raid");
	bot::help("remloot", "$MODULE_NAME/flatroll.txt", "leader", "Removing an item from a flatroll list", "Raid");
	bot::help("flatroll", "$MODULE_NAME/flatroll.txt", "leader", "Flatroll an item", "Raid");
	bot::help("multiloot", "$MODULE_NAME/flatroll.txt", "leader", "Adding multiple of an item to be rolled", "Raid");

?>
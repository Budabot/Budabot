<?php
	$MODULE_NAME = "RAID_MODULE";

	//Loot list and adding/removing of players	
	Command::register($MODULE_NAME, "", "loot.php", "loot", "leader", "Adds an item to the loot list");
	Command::register($MODULE_NAME, "", "multiloot.php", "multiloot", "leader", "Adds items using multiloot");
	Command::register($MODULE_NAME, "", "loot.php", "clear", "leader", "Clears the loot list");
	Command::register($MODULE_NAME, "", "remloot.php", "remloot", "leader", "Remove item from loot list");
	Command::register($MODULE_NAME, "", "reroll.php", "reroll", "leader", "Rerolls the residual loot list");
	Command::register($MODULE_NAME, "", "rollloot.php", "rollloot", "leader", "Rolls the loot list");
	CommandAlias::register($MODULE_NAME, "rollloot", "flatroll");
	CommandAlias::register($MODULE_NAME, "rollloot", "result");
	CommandAlias::register($MODULE_NAME, "rollloot", "win");
	
	Command::register($MODULE_NAME, "", "list.php", "list", "all", "Shows the loot list");
	Command::register($MODULE_NAME, "", "add.php", "add", "all", "Let a player adding to a slot");	
	Command::register($MODULE_NAME, "", "rem.php", "rem", "all", "Let a player removing from a slot");
	
	//APFs
	Command::register($MODULE_NAME, "", "13.php", "13", "leader", "Adds apf13 loot list");
	Command::register($MODULE_NAME, "", "28.php", "28", "leader", "Adds apf28 loot list");
	Command::register($MODULE_NAME, "", "35.php", "35", "leader", "Adds apf35 loot list");
	
	// Loottable for the different APF Sectors
	Command::register($MODULE_NAME, "", "loottable.php", "loottable", "all", "Shows what drops of APF Boss");
	
	// Guides for the different APF items
	Command::register($MODULE_NAME, "", "apfloot.php", "apfloot", "all", "Shows what to make from apf items");
	
	// DB loot manager
	Command::register($MODULE_NAME, "", "dbloot.php", "dbloot", "leader", "Used to add DB loot to the loot list");
	Command::register($MODULE_NAME, "", "dbloot.php", "db2loot", "leader", "Used to add DB loot to the loot list");
	Command::register($MODULE_NAME, "", "dbloot.php", "db1", "leader", "Shows Possible DB1 Armor/NCUs/Programs");
	Command::register($MODULE_NAME, "", "dbloot.php", "db2", "leader", "Shows Possible DB2 Armor");
	
	// Pande loot manager
	Command::register($MODULE_NAME, "", "pandeloot.php", "beastarmor", "all", "Shows Possible Beast Armor Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "beastweaps", "all", "Shows Possible Beast Weapons Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "beaststars", "all", "Shows Possible Beast Stars Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "tnh", "all", "Shows Possible The Night Heart Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "sb", "all", "Shows Possible Shadowbreeds Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "aries", "all", "Shows Possible Aries Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "leo", "all", "Shows Possible Leo Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "virgo", "all", "Shows Possible Virgo Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "aquarius", "all", "Shows Possible Aquarius Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "cancer", "all", "Shows Possible Cancer Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "gemini", "all", "Shows Possible Gemini Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "libra", "all", "Shows Possible Libra Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "pisces", "all", "Shows Possible Pisces Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "taurus", "all", "Shows Possible Taurus Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "capricorn", "all", "Shows Possible Capricorn Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "sagittarius", "all", "Shows Possible Sagittarius Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "scorpio", "all", "Shows Possible Scorpio Zodiac Loot");
	Command::register($MODULE_NAME, "", "pandeloot.php", "pandeloot", "leader", "used to add pande loot to the loot list");
	Command::register($MODULE_NAME, "", "pandeloot.php", "pande", "all", "shows Initial list of pande bosses");
	
	// Albtraum loot manager
	Command::register($MODULE_NAME, "", "albloot.php", "alb", "leader", "Shows Possible Albtraum loots");
	Command::register($MODULE_NAME, "", "albloot.php", "albloot", "leader", "Used to add Albtraum loot to the loot list");
	
	// Settings
	Setting::add($MODULE_NAME, "add_on_loot", "Adding to loot show on", "edit", "options", "2", "tells;privatechat;privatechat and tells", '1;2;3', "mod");

	// Help files
	Help::register($MODULE_NAME, "add", "add_rem.txt", "all", "Adding to a lootitem");
	Help::register($MODULE_NAME, "rem", "add_rem.txt", "all", "Removing your bid on a lootitem");
	Help::register($MODULE_NAME, "loot", "flatroll.txt", "leader", "Adding an item to be flatrolled");
	Help::register($MODULE_NAME, "remloot", "flatroll.txt", "leader", "Removing an item from a flatroll list");
	Help::register($MODULE_NAME, "flatroll", "flatroll.txt", "leader", "Flatroll an item");
	Help::register($MODULE_NAME, "multiloot", "flatroll.txt", "leader", "Adding multiple of an item to be rolled");
	Help::register($MODULE_NAME, "apfloot", "apfloot.txt", "guild", "Show the Loots of the APF");
	Help::register($MODULE_NAME, "dbloot", "dbloot.txt", "all", "Loot manager for DB1/DB2 Instance");
	Help::register($MODULE_NAME, "pande", "pande.txt", "all", "Loot manager for Pandemonium Raid loot");
	Help::register($MODULE_NAME, "albloot", "albloot.txt", "all", "Loot manager for Albtraum Instance");
?>
<?php
	require_once 'Raid.class.php';

	$MODULE_NAME = "RAID_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'raid_loot');

	//Loot list and adding/removing of players	
	Command::register($MODULE_NAME, "", "loot.php", "loot", "leader", "Adds an item to the loot list", 'flatroll');
	Command::register($MODULE_NAME, "", "multiloot.php", "multiloot", "leader", "Adds items using multiloot", 'flatroll');
	Command::register($MODULE_NAME, "", "remloot.php", "remloot", "leader", "Remove item from loot list", 'flatroll');
	Command::register($MODULE_NAME, "", "reroll.php", "reroll", "leader", "Rerolls the residual loot list", 'flatroll');
	Command::register($MODULE_NAME, "", "flatroll.php", "flatroll", "leader", "Rolls the loot list", 'flatroll');
	CommandAlias::register($MODULE_NAME, "flatroll", "rollloot");
	CommandAlias::register($MODULE_NAME, "flatroll", "result");
	CommandAlias::register($MODULE_NAME, "flatroll", "win");
	
	Command::register($MODULE_NAME, "", "list.php", "list", "all", "Shows the loot list", 'flatroll');
	Command::register($MODULE_NAME, "", "add.php", "add", "all", "Let a player adding to a slot", 'add_rem');	
	Command::register($MODULE_NAME, "", "rem.php", "rem", "all", "Let a player removing from a slot", 'add_rem');
	
	// APFs
	Command::register($MODULE_NAME, "", "13.php", "13", "leader", "Adds apf 13 loot list", 'apfloot');
	Command::register($MODULE_NAME, "", "28.php", "28", "leader", "Adds apf 28 loot list", 'apfloot');
	Command::register($MODULE_NAME, "", "35.php", "35", "leader", "Adds apf 35 loot list", 'apfloot');
	Command::register($MODULE_NAME, "", "apf.php", "apf", "all", "Shows what drops of APF Boss", 'apfloot');
	Command::register($MODULE_NAME, "", "apfloot.php", "apfloot", "all", "Shows what to make from apf items", 'apfloot');
	
	// DB loot manager
	Command::register($MODULE_NAME, "", "dbloot.php", "db1", "leader", "Shows Possible DB1 Armor/NCUs/Programs", 'dbloot');
	Command::register($MODULE_NAME, "", "dbloot.php", "db2", "leader", "Shows Possible DB2 Armor", 'dbloot');
	
	// Pande loot manager
	Command::register($MODULE_NAME, "", "pandeloot.php", "beastarmor", "all", "Shows Possible Beast Armor Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "beastweaps", "all", "Shows Possible Beast Weapons Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "beaststars", "all", "Shows Possible Beast Stars Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "tnh", "all", "Shows Possible The Night Heart Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "sb", "all", "Shows Possible Shadowbreeds Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "aries", "all", "Shows Possible Aries Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "leo", "all", "Shows Possible Leo Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "virgo", "all", "Shows Possible Virgo Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "aquarius", "all", "Shows Possible Aquarius Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "cancer", "all", "Shows Possible Cancer Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "gemini", "all", "Shows Possible Gemini Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "libra", "all", "Shows Possible Libra Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "pisces", "all", "Shows Possible Pisces Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "taurus", "all", "Shows Possible Taurus Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "capricorn", "all", "Shows Possible Capricorn Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "sagittarius", "all", "Shows Possible Sagittarius Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "scorpio", "all", "Shows Possible Scorpio Zodiac Loot", 'pande');
	Command::register($MODULE_NAME, "", "pandeloot.php", "pande", "all", "shows Initial list of pande bosses", 'pande');
	
	// Albtraum loot manager
	Command::register($MODULE_NAME, "", "albloot.php", "alb", "leader", "Shows Possible Albtraum loots", 'albloot');
	
	// Xan loot manager
	Command::register($MODULE_NAME, "", "xan.php", "xan", "all", "Shows Possible Legacy of the Xan Loot", 'xan');
	Command::register($MODULE_NAME, "", "xan.php", "vortexx", "all", "Shows Possible Vortexx Loot", 'xan');
	Command::register($MODULE_NAME, "", "xan.php", "mitaar", "all", "Shows Possible Mitaar Hero Loot", 'xan');
	Command::register($MODULE_NAME, "", "xan.php", "12m", "all", "Shows Possible 12 man Loot", 'xan');
	
	// Settings
	Setting::add($MODULE_NAME, "add_on_loot", "Adding to loot show on", "edit", "options", "2", "tells;privatechat;privatechat and tells", '1;2;3', "mod");

	// Help files
	Help::register($MODULE_NAME, "add_rem", "add_rem.txt", "all", "Adding to a lootitem");
	Help::register($MODULE_NAME, "flatroll", "flatroll.txt", "leader", "Flatroll an item");
	
	Help::register($MODULE_NAME, "apfloot", "apfloot.txt", "guild", "Show the Loots of the APF");
	Help::register($MODULE_NAME, "dbloot", "dbloot.txt", "all", "Loot manager for DB1/DB2 Instance");
	Help::register($MODULE_NAME, "pande", "pande.txt", "all", "Loot manager for Pandemonium Raid loot");
	Help::register($MODULE_NAME, "albloot", "albloot.txt", "all", "Loot manager for Albtraum Instance");
	Help::register($MODULE_NAME, "xan", "xan.txt", "all", "Loot manager for Xan playfield");
?>
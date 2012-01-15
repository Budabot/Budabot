<?php
	require_once 'Raid.class.php';

	$db->loadSQLFile($MODULE_NAME, 'raid_loot');

	// Loot list and adding/removing of players	
	$command->register($MODULE_NAME, "", "loot.php", "loot", "leader", "Adds an item to the loot list", 'flatroll');
	$command->register($MODULE_NAME, "", "multiloot.php", "multiloot", "leader", "Adds items using multiloot", 'flatroll');
	$command->register($MODULE_NAME, "", "remloot.php", "remloot", "leader", "Remove item from loot list", 'flatroll');
	$command->register($MODULE_NAME, "", "reroll.php", "reroll", "leader", "Rerolls the residual loot list", 'flatroll');
	$command->register($MODULE_NAME, "", "flatroll.php", "flatroll", "leader", "Rolls the loot list", 'flatroll');
	$commandAlias->register($MODULE_NAME, "flatroll", "rollloot");
	$commandAlias->register($MODULE_NAME, "flatroll", "result");
	$commandAlias->register($MODULE_NAME, "flatroll", "win");
	
	$command->register($MODULE_NAME, "", "list.php", "list", "all", "Shows the loot list", 'flatroll');
	$command->register($MODULE_NAME, "", "add.php", "add", "all", "Let a player adding to a slot", 'add_rem');	
	$command->register($MODULE_NAME, "", "rem.php", "rem", "all", "Let a player removing from a slot", 'add_rem');
	
	// APFs
	$command->register($MODULE_NAME, "", "apfsector.php", "13", "leader", "Adds APF 13 loot to the loot list", 'apfloot');
	$command->register($MODULE_NAME, "", "apfsector.php", "28", "leader", "Adds APF 28 loot to the loot list", 'apfloot');
	$command->register($MODULE_NAME, "", "apfsector.php", "35", "leader", "Adds APF 35 loot to the loot list", 'apfloot');
	$command->register($MODULE_NAME, "", "apf.php", "apf", "all", "Shows what drops of APF Boss", 'apfloot');
	$command->register($MODULE_NAME, "", "apfloot.php", "apfloot", "all", "Shows what to make from apf items", 'apfloot');
	
	// DB loot manager
	$command->register($MODULE_NAME, "", "dbloot.php", "db1", "leader", "Shows Possible DB1 Armor/NCUs/Programs", 'dbloot');
	$command->register($MODULE_NAME, "", "dbloot.php", "db2", "leader", "Shows Possible DB2 Armor", 'dbloot');
	
	// Pande loot manager
	$command->register($MODULE_NAME, "", "pandeloot.php", "beastarmor", "all", "Shows Beast Armor loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "beastweaps", "all", "Shows Beast Weapons loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "beaststars", "all", "Shows Beast Stars loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "tnh", "all", "Shows The Night Heart loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "sb", "all", "Shows Shadowbreeds loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "aries", "all", "Shows Aries Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "leo", "all", "Shows Leo Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "virgo", "all", "Shows Virgo Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "aquarius", "all", "Shows Aquarius Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "cancer", "all", "Shows Cancer Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "gemini", "all", "Shows Gemini Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "libra", "all", "Shows Libra Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "pisces", "all", "Shows Pisces Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "taurus", "all", "Shows Taurus Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "capricorn", "all", "Shows Capricorn Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "sagittarius", "all", "Shows Sagittarius Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "scorpio", "all", "Shows Scorpio Zodiac loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "bastion", "all", "Shows Bastion loot", 'pande');
	$command->register($MODULE_NAME, "", "pandeloot.php", "pande", "all", "Shows list of pande bosses and loot categories", 'pande');
	
	// Albtraum loot manager
	$command->register($MODULE_NAME, "", "albloot.php", "alb", "leader", "Shows Possible Albtraum loots", 'albloot');
	
	// Xan loot manager
	$command->register($MODULE_NAME, "", "xan.php", "xan", "all", "Shows Possible Legacy of the Xan Loot", 'xan');
	$command->register($MODULE_NAME, "", "xan.php", "vortexx", "all", "Shows Possible Vortexx Loot", 'xan');
	$command->register($MODULE_NAME, "", "xan.php", "mitaar", "all", "Shows Possible Mitaar Hero Loot", 'xan');
	$command->register($MODULE_NAME, "", "xan.php", "12m", "all", "Shows Possible 12 man Loot", 'xan');
	
	// Settings
	$setting->add($MODULE_NAME, "add_on_loot", "Adding to loot show on", "edit", "options", "2", "tells;privatechat;privatechat and tells", '1;2;3', "mod");

	// Help files
	$help->register($MODULE_NAME, "add_rem", "add_rem.txt", "all", "Adding to a lootitem");
	$help->register($MODULE_NAME, "flatroll", "flatroll.txt", "leader", "Flatroll an item");
	$help->register($MODULE_NAME, "apfloot", "apfloot.txt", "guild", "Show the Loots of the APF");
	$help->register($MODULE_NAME, "dbloot", "dbloot.txt", "all", "Loot manager for DB1/DB2 Instance");
	$help->register($MODULE_NAME, "pande", "pande.txt", "all", "Loot manager for Pandemonium Raid loot");
	$help->register($MODULE_NAME, "albloot", "albloot.txt", "all", "Loot manager for Albtraum Instance");
	$help->register($MODULE_NAME, "xan", "xan.txt", "all", "Loot manager for Xan playfield");
?>
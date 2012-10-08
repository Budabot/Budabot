<?php
	require_once 'Raid.class.php';

	$db->loadSQLFile($MODULE_NAME, 'raid_loot');

	// Loot list and adding/removing of players
	$command->register($MODULE_NAME, "", "loot.php", "loot", "rl", "Adds an item to the loot list", "flatroll.txt");
	$command->register($MODULE_NAME, "", "multiloot.php", "multiloot", "rl", "Adds items using multiloot", "flatroll.txt");
	$command->register($MODULE_NAME, "", "remloot.php", "remloot", "rl", "Remove item from loot list", "flatroll.txt");
	$command->register($MODULE_NAME, "", "reroll.php", "reroll", "rl", "Rerolls the residual loot list", "flatroll.txt");
	$command->register($MODULE_NAME, "", "flatroll.php", "flatroll", "rl", "Rolls the loot list", "flatroll.txt");
	$commandAlias->register($MODULE_NAME, "flatroll", "rollloot");
	$commandAlias->register($MODULE_NAME, "flatroll", "result");
	$commandAlias->register($MODULE_NAME, "flatroll", "win");

	$command->register($MODULE_NAME, "", "list.php", "list", "all", "Shows the loot list", "flatroll.txt");
	$command->register($MODULE_NAME, "", "add.php", "add", "all", "Let a player adding to a slot", "add_rem.txt");
	$command->register($MODULE_NAME, "", "rem.php", "rem", "all", "Let a player removing from a slot", "add_rem.txt");

	// APFs
	$command->register($MODULE_NAME, "", "apfsector.php", "13", "rl", "Adds APF 13 loot to the loot list", "apf.txt");
	$command->register($MODULE_NAME, "", "apfsector.php", "28", "rl", "Adds APF 28 loot to the loot list", "apf.txt");
	$command->register($MODULE_NAME, "", "apfsector.php", "35", "rl", "Adds APF 35 loot to the loot list", "apf.txt");
	$command->register($MODULE_NAME, "", "apf.php", "apf", "all", "Shows what drops of APF Boss", "apf.txt");

	// DB loot manager
	$command->register($MODULE_NAME, "", "dbloot.php", "db1", "rl", "Shows Possible DB1 Armor/NCUs/Programs", "dbloot.txt");
	$command->register($MODULE_NAME, "", "dbloot.php", "db2", "rl", "Shows Possible DB2 Armor", "dbloot.txt");

	// Pande loot manager
	$command->register($MODULE_NAME, "", "pandeloot.php", "beastarmor", "all", "Shows Beast Armor loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "beastweaps", "all", "Shows Beast Weapons loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "beaststars", "all", "Shows Beast Stars loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "tnh", "all", "Shows The Night Heart loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "sb", "all", "Shows Shadowbreeds loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "aries", "all", "Shows Aries Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "leo", "all", "Shows Leo Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "virgo", "all", "Shows Virgo Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "aquarius", "all", "Shows Aquarius Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "cancer", "all", "Shows Cancer Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "gemini", "all", "Shows Gemini Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "libra", "all", "Shows Libra Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "pisces", "all", "Shows Pisces Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "taurus", "all", "Shows Taurus Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "capricorn", "all", "Shows Capricorn Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "sagittarius", "all", "Shows Sagittarius Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "scorpio", "all", "Shows Scorpio Zodiac loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "bastion", "all", "Shows Bastion loot", 'pande.txt');
	$command->register($MODULE_NAME, "", "pandeloot.php", "pande", "all", "Shows list of pande bosses and loot categories", 'pande.txt');

	// Albtraum loot manager
	$command->register($MODULE_NAME, "", "albloot.php", "alb", "rl", "Shows Possible Albtraum loots", "albloot.txt");

	// Xan loot manager
	$command->register($MODULE_NAME, "", "xan.php", "xan", "all", "Shows Possible Legacy of the Xan Loot", "xan.txt");
	$command->register($MODULE_NAME, "", "xan.php", "vortexx", "all", "Shows Possible Vortexx Loot", "xan.txt");
	$command->register($MODULE_NAME, "", "xan.php", "mitaar", "all", "Shows Possible Mitaar Hero Loot", "xan.txt");
	$command->register($MODULE_NAME, "", "xan.php", "12m", "all", "Shows Possible 12 man Loot", "xan.txt");

	// Settings
	$setting->add($MODULE_NAME, "add_on_loot", "Adding to loot show on", "edit", "options", "2", "tells;privatechat;privatechat and tells", '1;2;3', "mod");
?>

<?php
	require_once 'db_utils.php';

	$MODULE_NAME = "EXTRAHELPBOT_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "dyna");
	bot::loadSQLFile($MODULE_NAME, "research");
 
	bot::command("", "$MODULE_NAME/mobloot.php", "mobloot", "all", "loot QL Infos ");
	bot::command("", "$MODULE_NAME/random.php", "random", "all", "Random order");
	bot::command("", "$MODULE_NAME/cluster.php", "cluster", "all", "cluster location");
	bot::command("", "$MODULE_NAME/buffitem.php", "buffitem", "all", "buffitem look up");
	bot::command("", "$MODULE_NAME/whatbuffs.php", "whatbuffs", "all", "find items that buff");
	bot::command("", "$MODULE_NAME/dyna.php", "dyna", "all", "Search for RK Dynaboss");
	bot::command("", "$MODULE_NAME/research.php", "research", "all", "Info on Research");
	
	//Max XP calculator
	bot::command("", "$MODULE_NAME/cap_xp.php", "capsk", "all", "Max SK Calculator");
	bot::command("", "$MODULE_NAME/cap_xp.php", "capxp", "all", "Max XP Calculator");
	
	bot::help("buffitem", "$MODULE_NAME/buffitem.txt", "all", "How to use buffitem");
	bot::help("cluster", "$MODULE_NAME/cluster.txt", "all", "How to use cluster");
	bot::help("mobloot", "$MODULE_NAME/mobloot.txt", "all", "How to use mobloot");
	bot::help("whatbuffs", "$MODULE_NAME/whatbuffs.txt", "all", "How to use whatbuffs");
	bot::help("dyna", "$MODULE_NAME/dyna.txt", "all", "Search for RK Dynaboss");
	bot::help("research", "$MODULE_NAME/research.txt", "all", "Info on Research");
	bot::help("capxp", "$MODULE_NAME/capxp.txt", "all", "Set your reasearch bar for max xp/sk");
	
?>

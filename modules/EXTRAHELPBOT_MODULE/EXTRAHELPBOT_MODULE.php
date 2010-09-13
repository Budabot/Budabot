<?php
	require_once 'db_utils.php';

	$MODULE_NAME = "EXTRAHELPBOT_MODULE";
 
	bot::command("", "$MODULE_NAME/mobloot.php", "mobloot", "all", "loot QL Infos ");
	bot::command("", "$MODULE_NAME/random.php", "random", "all", "Random order");
	bot::command("", "$MODULE_NAME/cluster.php", "cluster", "all", "cluster location");
	bot::command("", "$MODULE_NAME/buffitem.php", "buffitem", "all", "buffitem look up");
	bot::command("", "$MODULE_NAME/whatbuffs.php", "whatbuffs", "all", "find items that buff");
	
	bot::help("buffitem", "$MODULE_NAME/buffitem.txt", "all", "How to use buffitem");
	bot::help("cluster", "$MODULE_NAME/cluster.txt", "all", "How to use cluster");
	bot::help("mobloot", "$MODULE_NAME/mobloot.txt", "all", "How to use mobloot");
	bot::help("whatbuffs", "$MODULE_NAME/whatbuffs.txt", "all", "How to use whatbuffs");
	
?>

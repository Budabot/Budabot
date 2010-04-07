<?php
	$MODULE_NAME = "EXTRAHELPBOT_MODULE";
	$PLUGIN_VERSION = 0.1;
	$FOLDER = $dir;
 
	//Loot QL calc
	bot::command("", "$MODULE_NAME/loot.php", "loot", "all", "loot QL Infos ");
	
	//random module
	bot::command("", "$MODULE_NAME/random.php", "random", "all", "Random order");
	
	//cluster module
	bot::command("", "$MODULE_NAME/cluster.php", "cluster", "all", "cluster location");
	
	//buff items module
	bot::command("", "$MODULE_NAME/buffitem.php", "buffitem", "all", "buffitem look up");
	
	bot::command("", "$MODULE_NAME/whatbuffs.php", "whatbuffs", "all", "find items that buff");
	
?>

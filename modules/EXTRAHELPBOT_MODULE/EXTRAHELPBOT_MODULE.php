<?php
$MODULE_NAME = "EXTRAHELPBOT_MODULE";
$PLUGIN_VERSION = 0.1;
$FOLDER = $dir;
 
	//Loot QL calc
	bot::command("guild", "$MODULE_NAME/loot.php", "loot", "all", "loot QL Infos ");
	bot::command("msg", "$MODULE_NAME/loot.php", "loot", "all", "loot QL Infos ");
	bot::command("priv", "$MODULE_NAME/loot.php", "loot", "all", "loot QL Infos ");
	
	//random module
	bot::command("guild", "$MODULE_NAME/random.php", "random", "all", "Random order");
	bot::command("msg", "$MODULE_NAME/random.php", "random", "all", "Random order");
	bot::command("priv", "$MODULE_NAME/random.php", "random", "all", "Random order");
	
	//cluster module
	bot::command("guild", "$MODULE_NAME/cluster.php", "cluster", "all", "cluster location");
	bot::command("msg", "$MODULE_NAME/cluster.php", "cluster", "all", "cluster location");
	bot::command("priv", "$MODULE_NAME/cluster.php", "cluster", "all", "cluster location");
	
	//buff items module
	bot::command("guild", "$MODULE_NAME/buffitem.php", "buffitem", "all", "buffitem look up");
	bot::command("msg", "$MODULE_NAME/buffitem.php", "buffitem", "all", "buffitem look up");
	bot::command("priv", "$MODULE_NAME/buffitem.php", "buffitem", "all", "buffitem look up");
	
	bot::command("guild", "$MODULE_NAME/whatbuffs.php", "whatbuffs", "all", "find items that buff");
	bot::command("msg", "$MODULE_NAME/whatbuffs.php", "whatbuffs", "all", "find items that buff");
	bot::command("priv", "$MODULE_NAME/whatbuffs.php", "whatbuffs", "all", "find items that buff");
	
?>

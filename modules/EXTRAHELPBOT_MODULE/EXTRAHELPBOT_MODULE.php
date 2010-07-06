<?php
	$MODULE_NAME = "EXTRAHELPBOT_MODULE";
	$PLUGIN_VERSION = 0.1;
 
	$this->command("", "$MODULE_NAME/mobloot.php", "mobloot", ALL, "loot QL Infos ");
	$this->command("", "$MODULE_NAME/random.php", "random", ALL, "Random order");
	$this->command("", "$MODULE_NAME/cluster.php", "cluster", ALL, "cluster location");
	$this->command("", "$MODULE_NAME/buffitem.php", "buffitem", ALL, "buffitem look up");
	$this->command("", "$MODULE_NAME/whatbuffs.php", "whatbuffs", ALL, "find items that buff");
	
?>

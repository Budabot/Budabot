<?php
	$MODULE_NAME = "DUST_BRIGADE_MODULE";

	// DB loot manager
	Command::register($MODULE_NAME, "", "dbloot.php", "dbloot", "leader", "Used to add DB loot to the loot list");
	Command::register($MODULE_NAME, "", "dbloot.php", "db2loot", "leader", "Used to add DB loot to the loot list");
	Command::register($MODULE_NAME, "", "dbloot.php", "db1", "leader", "Shows Possible DB1 Armor/NCUs/Programs");
	Command::register($MODULE_NAME, "", "dbloot.php", "db2", "leader", "Shows Possible DB2 Armor");

	//Helpfiles
	Help::register($MODULE_NAME, "dbloot", "dbloot.txt", "all", "Loot manager for DB1/DB2 Instance");

?>

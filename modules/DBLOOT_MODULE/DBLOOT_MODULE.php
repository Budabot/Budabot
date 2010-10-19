<?php
	$MODULE_NAME = "DBLOOT_MODULE";

	// DB loot manager
	bot::command("", "$MODULE_NAME/dbloot.php", "dbloot", "leader", "Used to add DB loot to the loot list");
	bot::command("", "$MODULE_NAME/dbloot.php", "db2loot", "leader", "Used to add DB loot to the loot list");
	bot::command("", "$MODULE_NAME/dbloot.php", "db1", "leader", "Shows Possible DB1 Armor/NCUs/Programs");
	bot::command("", "$MODULE_NAME/dbloot.php", "db2", "leader", "Shows Possible DB2 Armor");

	//Helpfiles
	bot::help("dbloot", "$MODULE_NAME/dbloot.txt", "all", "Loot manager for DB1/DB2 Instance");

?>

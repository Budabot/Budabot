<?php
	$MODULE_NAME = "ALB_MODULE";

	// Albtraum loot manager
	Command::register($MODULE_NAME, "", "albloot.php", "alb", "leader", "Shows Possible Albtraum loots");
	Command::register($MODULE_NAME, "", "albloot.php", "albloot", "leader", "Used to add Albtraum loot to the loot list");

	//Helpfiles, ez fog a helpfileban megjelenni, ha a linkre kattintunk akkor pedig az albloot.txt-t nyitja meg
	Help::register($MODULE_NAME, "albloot", "albloot.txt", "all", "Loot manager for Albtraum Instance");

?>

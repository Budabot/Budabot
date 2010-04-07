<?php
	$MODULE_NAME = "FUN_MODULE";
	$PLUGIN_VERSION = 1.0;

	// Ding
	bot::command("", "$MODULE_NAME/ding.php", "ding", "all", "Shows a random ding gratz message.");

	// Doh
	bot::command("", "$MODULE_NAME/doh.php", "doh", "all", "Shows a random doh message.");

	// Beer
	bot::command("", "$MODULE_NAME/beer.php", "beer", "all", "Shows a random beer message.");

	// Cybor
	bot::command("", "$MODULE_NAME/cybor.php", "cybor", "all", "Shows a random cybor message.");

	// Chuck
	bot::command("", "$MODULE_NAME/chuck.php", "chuck", "all", "Shows a random Chuck Norris joke.");

	//Credz
	bot::command("", "$MODULE_NAME/credz.php", "credz", "all", "Shows a random credits message.");

	//homer
	bot::command("", "$MODULE_NAME/homer.php", "homer", "all", "Shows a random homer quote message.");

	//fight
	bot::command("", "$MODULE_NAME/fight.php", "fight", "all", "Let two persons fight against each other.");

	//Helpfiles
	bot::help("fun_module", "$MODULE_NAME/fun_module.txt", "guild", 'Fun commands', "Fun Module");
?>
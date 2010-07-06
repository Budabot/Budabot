<?php
	$MODULE_NAME = "FUN_MODULE";
	$PLUGIN_VERSION = 1.0;

	// Ding
	bot::command("", "$MODULE_NAME/ding.php", "ding", ALL, "Shows a random ding gratz message.");

	// Doh
	bot::command("", "$MODULE_NAME/doh.php", "doh", ALL, "Shows a random doh message.");

	// Beer
	bot::command("", "$MODULE_NAME/beer.php", "beer", ALL, "Shows a random beer message.");

	// Cybor
	bot::command("", "$MODULE_NAME/cybor.php", "cybor", ALL, "Shows a random cybor message.");

	// Chuck
	bot::command("", "$MODULE_NAME/chuck.php", "chuck", ALL, "Shows a random Chuck Norris joke.");

	//Credz
	bot::command("", "$MODULE_NAME/credz.php", "credz", ALL, "Shows a random credits message.");

	//homer
	bot::command("", "$MODULE_NAME/homer.php", "homer", ALL, "Shows a random homer quote message.");

	//fight
	bot::command("", "$MODULE_NAME/fight.php", "fight", ALL, "Let two persons fight against each other.");

	//Help files
	bot::help("fun_module", "$MODULE_NAME/fun_module.txt", ALL, 'Fun commands', "Fun Module");
?>
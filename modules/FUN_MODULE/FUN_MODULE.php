<?php
	$MODULE_NAME = "FUN_MODULE";
	$PLUGIN_VERSION = 1.0;

	// Ding
	$this->command("", "$MODULE_NAME/ding.php", "ding", ALL, "Shows a random ding gratz message.");

	// Doh
	$this->command("", "$MODULE_NAME/doh.php", "doh", ALL, "Shows a random doh message.");

	// Beer
	$this->command("", "$MODULE_NAME/beer.php", "beer", ALL, "Shows a random beer message.");

	// Cybor
	$this->command("", "$MODULE_NAME/cybor.php", "cybor", ALL, "Shows a random cybor message.");

	// Chuck
	$this->command("", "$MODULE_NAME/chuck.php", "chuck", ALL, "Shows a random Chuck Norris joke.");

	//Credz
	$this->command("", "$MODULE_NAME/credz.php", "credz", ALL, "Shows a random credits message.");

	//homer
	$this->command("", "$MODULE_NAME/homer.php", "homer", ALL, "Shows a random homer quote message.");

	//fight
	$this->command("", "$MODULE_NAME/fight.php", "fight", ALL, "Let two persons fight against each other.");

	//Help files
	$this->help("fun_module", "$MODULE_NAME/fun_module.txt", ALL, 'Fun commands', "Fun Module");
?>
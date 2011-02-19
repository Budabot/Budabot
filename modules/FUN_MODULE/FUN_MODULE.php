<?php
	$MODULE_NAME = "FUN_MODULE";

	Command::register($MODULE_NAME, "", "fc.php", "fc", "all", "Shows a random FC quote.");
	Command::register($MODULE_NAME, "", "doh.php", "doh", "all", "Shows a random doh message.");
	Command::register($MODULE_NAME, "", "beer.php", "beer", "all", "Shows a random beer message.");
	Command::register($MODULE_NAME, "", "cybor.php", "cybor", "all", "Shows a random cybor message.");
	Command::register($MODULE_NAME, "", "chuck.php", "chuck", "all", "Shows a random Chuck Norris joke.");
	Command::register($MODULE_NAME, "", "credz.php", "credz", "all", "Shows a random credits message.");
	Command::register($MODULE_NAME, "", "homer.php", "homer", "all", "Shows a random homer quote message.");
	Command::register($MODULE_NAME, "", "dwight.php", "dwight", "all", "Shows a random dwight quote message.");
	Command::register($MODULE_NAME, "", "brain.php", "brain", "all", "Shows a random pinky and the brain quote message.");
	CommandAlias::register($MODULE_NAME, "brain", "pinky");
	
	// Ding
	Command::register($MODULE_NAME, "", "ding.php", "ding", "all", "Shows a random ding gratz message.");

	//fight
	Command::register($MODULE_NAME, "", "fight.php", "fight", "all", "Let two persons fight against each other.");

	//Help files
	Help::register($MODULE_NAME, "fun_module", "fun_module.txt", "guild", 'Fun commands');
?>
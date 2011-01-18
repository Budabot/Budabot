<?php
	$MODULE_NAME = "FUN_MODULE";

	bot::command("", "$MODULE_NAME/doh.php", "doh", "all", "Shows a random doh message.");
	bot::command("", "$MODULE_NAME/beer.php", "beer", "all", "Shows a random beer message.");
	bot::command("", "$MODULE_NAME/cybor.php", "cybor", "all", "Shows a random cybor message.");
	bot::command("", "$MODULE_NAME/chuck.php", "chuck", "all", "Shows a random Chuck Norris joke.");
	bot::command("", "$MODULE_NAME/credz.php", "credz", "all", "Shows a random credits message.");
	bot::command("", "$MODULE_NAME/homer.php", "homer", "all", "Shows a random homer quote message.");
	bot::command("", "$MODULE_NAME/dwight.php", "dwight", "all", "Shows a random homer quote message.");
	
	// Ding
	bot::command("", "$MODULE_NAME/ding.php", "ding", "all", "Shows a random ding gratz message.");

	//fight
	bot::command("", "$MODULE_NAME/fight.php", "fight", "all", "Let two persons fight against each other.");

	//Help files
	bot::help($MODULE_NAME, "fun_module", "fun_module.txt", "guild", 'Fun commands');
?>
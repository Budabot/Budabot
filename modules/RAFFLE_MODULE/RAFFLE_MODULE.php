<?php

	$MODULE_NAME = "RAFFLE_MODULE";
        require_once 'raffle_func.php';
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//raffle message
	bot::command("", "$MODULE_NAME/raffle.php", "raffle", "all");
	
	//timer
	bot::event("2sec", "$MODULE_NAME/check_raffle.php", "raffle", "Checks to see if raffle is over");

	//Help files
	bot::help("Raffle", "$MODULE_NAME/raffle.txt", "all", "Start/Join/Leave Raffles", "Raffles");

	//Settings
	bot::addsetting("defaultraffletime", "Sets how long the raffle should go for in minutes.", "edit", 3, "number");

?>
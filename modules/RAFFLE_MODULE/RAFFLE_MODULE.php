<?php

	$MODULE_NAME = "RAFFLE_MODULE";
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//raffle message
	bot::command("", "$MODULE_NAME/raffle.php", "raffle", "all");
	bot::command("", "$MODULE_NAME/status.php", "rafflestatus", "all");
	bot::command("", "$MODULE_NAME/join.php", "joinRaffle", "all");
	bot::command("", "$MODULE_NAME/leave.php", "leaveRaffle", "all");

	//timer
	bot::event("2sec", "$MODULE_NAME/check_winner.php", "", "Checks to see if raffle is over");

	//Help files
	bot::help("Raffle", "$MODULE_NAME/raffle.txt", "guild", "Start/Join/Leave Raffles", "Raffles");

	//Settings
	bot::addsetting("defaultraffletime", "Sets how long the raffle should go for in minutes.", "edit", 3, "number");

?>
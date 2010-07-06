<?php

	$MODULE_NAME = "RAFFLE_MODULE";
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//raffle message
	bot::command("", "$MODULE_NAME/raffle.php", "raffle", GUILDMEMBER);
	bot::command("", "$MODULE_NAME/status.php", "rafflestatus", GUILDMEMBER);
	bot::command("", "$MODULE_NAME/join.php", "joinRaffle", GUILDMEMBER);
	bot::command("", "$MODULE_NAME/leave.php", "leaveRaffle", GUILDMEMBER);

	//timer
	bot::event("2sec", "$MODULE_NAME/check_winner.php", "", "Checks to see if raffle is over");

	//Help files
	bot::help("Raffle", "$MODULE_NAME/raffle.txt", GUILDMEMBER, "Start/Join/Leave Raffles");

	//Settings
	bot::addsetting("defaultraffletime", "Sets how long the raffle should go for in minutes.", "edit", 3, "number");

?>
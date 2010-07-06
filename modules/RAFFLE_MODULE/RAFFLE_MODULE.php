<?php

	$MODULE_NAME = "RAFFLE_MODULE";
	
	//Setup
	$this->event("setup", "$MODULE_NAME/setup.php");

	//raffle message
	$this->command("", "$MODULE_NAME/raffle.php", "raffle", GUILDMEMBER);
	$this->command("", "$MODULE_NAME/status.php", "rafflestatus", GUILDMEMBER);
	$this->command("", "$MODULE_NAME/join.php", "joinRaffle", GUILDMEMBER);
	$this->command("", "$MODULE_NAME/leave.php", "leaveRaffle", GUILDMEMBER);

	//timer
	$this->event("2sec", "$MODULE_NAME/check_winner.php", "", "Checks to see if raffle is over");

	//Help files
	$this->help("Raffle", "$MODULE_NAME/raffle.txt", GUILDMEMBER, "Start/Join/Leave Raffles");

	//Settings
	$this->addsetting("defaultraffletime", "Sets how long the raffle should go for in minutes.", "edit", 3, "number");

?>
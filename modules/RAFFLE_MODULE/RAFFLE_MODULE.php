<?php
   /*
   ** Author: Mindrila (RK1)
   ** Description: Raffle module, inspired by the implementation in BeBot
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://budabot.com/)
   **
   ** Date(created): 13.07.2010
   ** Date(last modified): 13.07.2010
   */

	require_once 'raffle_func.php';

	$MODULE_NAME = "RAFFLE_MODULE";

	// Setup
	Event::register($MODULE_NAME, "setup", "setup.php");

	// Raffle message
	Command::register($MODULE_NAME, "", "raffle.php", "raffle", "all");
	
	// Timer
	Event::register($MODULE_NAME, "2sec", "check_raffle.php", "raffle", "Checks to see if raffle is over");

	// Settings
	Setting::add($MODULE_NAME, "defaultraffletime", "Sets how long the raffle should go for in minutes.", "edit", "number", 3);

	// Help files
	Help::register($MODULE_NAME, "raffle", "raffle.txt", "all", "Start/Join/Leave Raffles", "Raffles");
?>
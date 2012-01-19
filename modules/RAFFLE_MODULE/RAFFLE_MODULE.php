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

	// Setup
	$event->register($MODULE_NAME, "setup", "setup.php");

	// Raffle message
	$command->register($MODULE_NAME, "", "raffle.php", "raffle", "all", "Raffle off items to players", "raffle.txt");
	
	// Timer
	$event->register($MODULE_NAME, "2sec", "check_raffle.php", "Checks to see if raffle is over");

	// Settings
	$setting->add($MODULE_NAME, "defaultraffletime", "How long the raffle should go for", "edit", "time", '3m', '1m;2m;3m;4m;5m', '', 'mod', "raffle.txt");
?>
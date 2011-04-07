<?php
	$MODULE_NAME = "WAITLIST_MODULE";
	
	// Setup
	Event::register($MODULE_NAME, "setup", "setup.php");

	// Commands
	Command::register($MODULE_NAME, "", "waitlist.php", "waitlist", "all", "Show/Set the Waitlist");

	// Helpfile
	Help::register($MODULE_NAME, "waitlist", "waitlist.txt", "all", "How to use waitlist");
?>
<?php
	// Commands
	Command::register($MODULE_NAME, "", "waitlist.php", "waitlist", "all", "Show/Set the Waitlist");

	// Helpfile
	Help::register($MODULE_NAME, "waitlist", "waitlist.txt", "all", "How to use waitlist");
?>
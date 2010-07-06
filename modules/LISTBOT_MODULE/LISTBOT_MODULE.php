<?php
	$MODULE_NAME = "LISTBOT_MODULE";
	
	//Setup
	$this->event("setup", "$MODULE_NAME/setup.php");

	//Commands
	$this->command("", "$MODULE_NAME/waitlist.php", "waitlist", ALL, "Show/Add the Waitlist");

	//Helpfile
    $this->help("waitlist", "$MODULE_NAME/waitlist.txt", ALL, "How to use the ListBot");
?>
<?php
	$MODULE_NAME = "LISTBOT_MODULE";
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Commands
	bot::command("", "$MODULE_NAME/waitlist.php", "waitlist", ALL, "Show/Add the Waitlist");

	//Helpfile
    bot::help("waitlist", "$MODULE_NAME/waitlist.txt", ALL, "How to use the ListBot");
?>
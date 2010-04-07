<?
	$MODULE_NAME = "LISTBOT_MODULE";

	//Commands
	bot::command("", "$MODULE_NAME/waitlist.php", "waitlist", "all", "Show/Add the Waitlist");
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Helpfile
    bot::help("waitlist", "$MODULE_NAME/waitlist.txt", "guild", "How to use the ListBot", "Listbot");
?>
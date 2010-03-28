<?
$MODULE_NAME = "LISTBOT_MODULE";

	//Commands
	bot::command("guild", "$MODULE_NAME/waitlist.php", "waitlist", "all", "Show/Add the Waitlist");
	bot::command("msg", "$MODULE_NAME/waitlist.php", "waitlist", "guild", "Show/Add the Waitlist");
	bot::command("priv", "$MODULE_NAME/waitlist.php", "waitlist", "all", "Show/Add the Waitlist");
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Helpfile
    bot::help("waitlist", "$MODULE_NAME/waitlist.txt", "guild", "How to use the ListBot", "Listbot");
?>
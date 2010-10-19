<?php 
	$MODULE_NAME = "EVENTS_MODULE";

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Commands
	bot::command("", "$MODULE_NAME/events.php", "events", "all", "Views events");
	bot::command("", "$MODULE_NAME/edit_event.php", "event", "rl", "Add/edit/remove events");
	bot::command("", "$MODULE_NAME/events.php", "joinevent", "all", "Join an event");
	bot::command("", "$MODULE_NAME/events.php", "leaveevent", "all", "Leave an event");
	bot::command("", "$MODULE_NAME/eventlist.php", "eventlist", "all", "View event attendees");
	
	//Helpfile
	bot::help("events", "$MODULE_NAME/events.txt", "all", "Adding/editing/removing events");
?>

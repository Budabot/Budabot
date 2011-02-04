<?php 
	$MODULE_NAME = "EVENTS_MODULE";

	//Setup
	Event::register($MODULE_NAME, "setup", "setup.php");

	//Commands
	bot::command("", "$MODULE_NAME/events.php", "events", "all", "Views events");
	bot::command("", "$MODULE_NAME/edit_event.php", "event", "rl", "Add/edit/remove events");
	bot::command("", "$MODULE_NAME/events.php", "joinevent", "all", "Join an event");
	bot::command("", "$MODULE_NAME/events.php", "leaveevent", "all", "Leave an event");
	bot::command("", "$MODULE_NAME/eventlist.php", "eventlist", "all", "View event attendees");
	
	//Helpfile
	Help::register($MODULE_NAME, "events", "events.txt", "all", "Adding/editing/removing events");
?>

<?php 
	$MODULE_NAME = "EVENTS_MODULE";
	$PLUGIN_VERSION = 1.0;
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Commands
	bot::command("", "$MODULE_NAME/events.php", "events", ALL, "Views events");
	bot::command("", "$MODULE_NAME/edit_event.php", "event", GUILDADMIN, "Add/edit/remove events");
	bot::command("", "$MODULE_NAME/events.php", "joinevent", ALL, "Join an event");
	bot::command("", "$MODULE_NAME/events.php", "leaveevent", ALL, "Leave an event");
	bot::command("", "$MODULE_NAME/eventlist.php", "eventlist", ALL, "View event attendees");
	
	//Helpfile
	bot::help("events", "$MODULE_NAME/events.txt", ALL, "Adding/editing/removing events");
?>

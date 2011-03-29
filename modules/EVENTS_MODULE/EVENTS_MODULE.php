<?php 
	$MODULE_NAME = "EVENTS_MODULE";

	//Setup
	DB::loadSQLFile($MODULE_NAME, "events");

	//Commands
	Command::register($MODULE_NAME, "", "events.php", "events", "all", "Views events");
	Command::register($MODULE_NAME, "", "edit_event.php", "event", "rl", "Add/edit/remove events");
	Command::register($MODULE_NAME, "", "events.php", "joinevent", "all", "Join an event");
	Command::register($MODULE_NAME, "", "events.php", "leaveevent", "all", "Leave an event");
	Command::register($MODULE_NAME, "", "eventlist.php", "eventlist", "all", "View event attendees");
	
	//Helpfile
	Help::register($MODULE_NAME, "events", "events.txt", "all", "Adding/editing/removing events");
?>

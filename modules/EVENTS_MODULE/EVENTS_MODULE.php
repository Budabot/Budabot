<?php 
	$MODULE_NAME = "EVENTS_MODULE";

	//Setup
	DB::loadSQLFile($MODULE_NAME, "events");

	//Commands
	Command::register($MODULE_NAME, "", "events.php", "events", "all", "Views events");
	Command::register($MODULE_NAME, "", "edit_event.php", "event", "rl", "Add/edit/remove events", 'events');
	Command::register($MODULE_NAME, "", "events.php", "joinevent", "all", "Join an event", 'events');
	Command::register($MODULE_NAME, "", "events.php", "leaveevent", "all", "Leave an event", 'events');
	Command::register($MODULE_NAME, "", "eventlist.php", "eventlist", "all", "View event attendees", 'events');
	
	//Helpfile
	Help::register($MODULE_NAME, "events", "events.txt", "all", "Adding/editing/removing events");
?>

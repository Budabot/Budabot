<?php 
	$MODULE_NAME = "EVENTS_MODULE";
	$PLUGIN_VERSION = 1.0;
	
	//Setup
	$this->event("setup", "$MODULE_NAME/setup.php");

	//Commands
	$this->command("", "$MODULE_NAME/events.php", "events", ALL, "Views events");
	$this->command("", "$MODULE_NAME/edit_event.php", "event", GUILDADMIN, "Add/edit/remove events");
	$this->command("", "$MODULE_NAME/events.php", "joinevent", ALL, "Join an event");
	$this->command("", "$MODULE_NAME/events.php", "leaveevent", ALL, "Leave an event");
	$this->command("", "$MODULE_NAME/eventlist.php", "eventlist", ALL, "View event attendees");
	
	//Helpfile
	$this->help("events", "$MODULE_NAME/events.txt", ALL, "Adding/editing/removing events");
?>

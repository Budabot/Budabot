<?php 
	require_once 'event_functions.php';

	DB::loadSQLFile($MODULE_NAME, "events");

	Command::register($MODULE_NAME, "", "events.php", "events", "all", "View/Edit events");
	CommandAlias::register($MODULE_NAME, "events", "event");
	
	Event::register($MODULE_NAME, "logOn", "events_logon.php", "none", "Show events to org members logging on");
	Event::register($MODULE_NAME, "joinPriv", "events_logon.php", "none", "Show events to characters joining the private channel");
	
	Help::register($MODULE_NAME, "events", "events.txt", "all", "Adding/editing/removing events");
?>

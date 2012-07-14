<?php
	require_once 'event_functions.php';

	$db->loadSQLFile($MODULE_NAME, "events");

	$command->register($MODULE_NAME, "", "events.php", "events", "all", "View/Join/Leave events", "events.txt");
	$subcommand->register($MODULE_NAME, "", "edit_events.php", "events add (.+)", "rl", "events", "Add an event");
	$subcommand->register($MODULE_NAME, "", "edit_events.php", "events rem (.+)", "rl", "events", "Remove an event");
	$subcommand->register($MODULE_NAME, "", "edit_events.php", "events setdesc (.+)", "rl", "events", "Change or set the description for an event");
	$subcommand->register($MODULE_NAME, "", "edit_events.php", "events setdate (.+)", "rl", "events", "Change or set the date for an event");

	$event->register($MODULE_NAME, "logOn", "events_logon.php", "Show events to org members logging on");
	$event->register($MODULE_NAME, "joinPriv", "events_logon.php", "Show events to characters joining the private channel");
?>

<?php
	$MODULE_NAME = "WEBCAST_MODULE";

	// Commands 
	Command::register($MODULE_NAME, "", "webcast.php", "webcast", "admin", "Sends your online list to a webserver");

	// Events
	Event::register($MODULE_NAME, "logOn", "webcast.php", "none", "Updates the list when someone logs on");
	Event::register($MODULE_NAME, "15mins", "webcast.php", "none", "Updates the list every 15 minutes");
//	Event::register($MODULE_NAME, "logOff", "webcast.php", "none", "Updates the list when someone logs off");
//  Removed for the time being, because Budabot processes logOff events prior to updating the online DB.
//  Instead the Module will update when someone logs on and every 15mins.

	// Settings
	Setting::add($MODULE_NAME, "webpath", "URL where your online list is sent", "edit", "text", "", "", "", "admin");

	//Helpiles
	Help::register($MODULE_NAME, "webcast", "webcast.txt", "admin", "How to use the webcast module.");
?>
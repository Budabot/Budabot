<?php
	$MODULE_NAME = "WEBCAST_MODULE";

	// Commands 
	Command::register($MODULE_NAME, "", "webcast.php", "webcast", "admin", "Sends your online list to a webserver");

	// Events
	Event::register($MODULE_NAME, "logOn", "webcast.php", "none", "Updates the list when someone logs on"); 
	Event::register($MODULE_NAME, "15mins", "webcast.php", "none", "Updates the list every 15 minutes"); 
//	bot::regevent("logOff", "$MODULE_NAME/webcast.php"); 
//  Removed for the time being, because Budabot processes logOff events prior to updating the online DB.
//  Instead the Module will update when someone logs on and every 15mins.

	// Settings
	Setting::add($MODULE_NAME, "webpath", "Where you online list is sent", "edit", "", "", "", "admin");

	//Helpiles
	Help::register($MODULE_NAME, "webcast", "webcast.txt", "admin", "How to use the webcast module.");
?>
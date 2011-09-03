<?php
	require_once 'Teamspeak3.class.php';

	$MODULE_NAME = "TEAMSPEAK3_MODULE";

	Command::register($MODULE_NAME, "", "ts.php", "ts", "all", "Show Status of the Teamspeak3 Server");

	Setting::add($MODULE_NAME, "ts_ip", "IP from the TS Server", "edit", "text", "Not set yet.", "", '', "mod");	
	Setting::add($MODULE_NAME, "ts_queryport", "Queryport for the TS Server", "edit", "number", "51234", "", '', "mod");
	Setting::add($MODULE_NAME, "ts_serverport", "Serverport for the TS Server", "edit", "number", "8767", "", '', "mod");
	Setting::add($MODULE_NAME, "ts_servername", "Name of the TS Server", "edit", "text", "Not set yet.", "", '', "mod");

	//Help files
    Help::register($MODULE_NAME, "ts", "ts.txt", "guild", "Using the Teamspeak3 plugin");
?>
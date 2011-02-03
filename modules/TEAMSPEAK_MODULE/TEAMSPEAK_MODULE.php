<?php
	$MODULE_NAME = "TEAMSPEAK_MODULE";

	bot::command("", "$MODULE_NAME/teamspeak_server.php", "ts", "all", "Show Status of the Teamspeak Server");
	
	Setting::add($MODULE_NAME, "ts_ip", "IP from the TS Server", "edit", "Not set yet.", "text", '0', "mod");	
	Setting::add($MODULE_NAME, "ts_queryport", "Queryport for the TS Server", "edit", "51234", "number", '0', "mod");
	Setting::add($MODULE_NAME, "ts_serverport", "Serverport for the TS Server", "edit", "8767", "number", '0', "mod");
	Setting::add($MODULE_NAME, "ts_servername", "Name of the TS Server", "edit", "Not set yet.", "text", '0', "mod");

	//Help files	
    bot::help($MODULE_NAME, "ts", "ts.txt", "guild", "Using the Teamspeak plugin");
?>
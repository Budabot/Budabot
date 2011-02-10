<?php
	$MODULE_NAME = "SERVER_STATUS_MODULE";

	//Server Status
	Command::register($MODULE_NAME, "", "server.php", "server", "all", "Shows the Server status");	

	//Help files
    Help::register($MODULE_NAME, "server", "server.txt", "guild", "Show the server status");
?>
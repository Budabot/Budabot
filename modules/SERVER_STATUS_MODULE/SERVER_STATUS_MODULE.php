<?php
	$MODULE_NAME = "SERVER_STATUS_MODULE";

	//Server Status
	bot::command("", "$MODULE_NAME/server_status.php", "server", "all", "Shows the Server status");	

	//Help files
    bot::help("serverstatus", "$MODULE_NAME/serverstatus.txt", "guild", "Show Serverstatus");
?>
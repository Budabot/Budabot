<?php
	$MODULE_NAME = "SERVER_STATUS_MODULE";

	//Server Status
	bot::command("", "$MODULE_NAME/server_status.php", "server", "all", "Shows the Server status");	

	//Help files
    bot::help($MODULE_NAME, "serverstatus", "serverstatus.txt", "guild", "Show Serverstatus");
?>
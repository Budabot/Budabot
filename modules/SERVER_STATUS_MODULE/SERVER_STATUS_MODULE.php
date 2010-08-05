<?php
	$MODULE_NAME = "SERVER_STATUS_MODULE";
	
	bot::event("1min", "$MODULE_NAME/check_server_status.php", 'none', "Checks the status of the server and updates bot with new status if it's changed");
	bot::addsetting("server_status", "no", "hide", "up");

	//Server Status
	bot::command("", "$MODULE_NAME/server_status.php", "server", "all", "Shows the Server status");	

	//Helpfiles
    bot::help("serverstatus", "$MODULE_NAME/serverstatus.txt", "guild", "Show Serverstatus", "Serverstatus");
?>
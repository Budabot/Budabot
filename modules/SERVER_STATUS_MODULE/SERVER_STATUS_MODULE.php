<?
$MODULE_NAME = "SERVER_STATUS_MODULE";
	
	bot::regevent("1min", "$MODULE_NAME/Check_Server_status.php");
	bot::addsetting("server_status", "no", "hide", "up");

	//Server Status
	bot::command("msg", "$MODULE_NAME/Server_status.php", "server", "all", "Shows the Server status");	
	bot::command("priv", "$MODULE_NAME/Server_status.php", "server", "guild", "Shows the Server status");	
	bot::command("guild", "$MODULE_NAME/Server_status.php", "server", "all", "Shows the Server status");	

	//Helpfiles
    bot::help("serverstatus", "$MODULE_NAME/serverstatus.txt", "guild", "Show Serverstatus", "Serverstatus");
?>
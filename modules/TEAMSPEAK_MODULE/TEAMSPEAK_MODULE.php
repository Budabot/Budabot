<?
$MODULE_NAME = "TEAMSPEAK_MODULE";

	bot::command("guild", "$MODULE_NAME/teamspeak_server.php", "ts", "all", "Show Status of the Teamspeak Server");
	bot::command("msg", "$MODULE_NAME/teamspeak_server.php", "ts", "guild", "Show Status of the Teamspeak Server");
	bot::command("priv", "$MODULE_NAME/teamspeak_server.php", "ts", "all", "Show Status of the Teamspeak Server");
	bot::addsetting("ts_ip", "IP from the TS Server", "edit", "Not set yet.", "text", '0', "mod");	
	bot::addsetting("ts_queryport", "Queryport for the TS Server", "edit", "51234", "number", '0', "mod");
	bot::addsetting("ts_serverport", "Serverport for the TS Server", "edit", "8767", "number", '0', "mod");
	bot::addsetting("ts_servername", "Name of the TS Server", "edit", "Not set yet.", "text", '0', "mod");

	//Helpfiles	
    bot::help("teamspeak", "$MODULE_NAME/ts.txt", "guild", "Using the Teamspeak plugin", "Teamspeak");
?>
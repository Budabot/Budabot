<?php
	$MODULE_NAME = "TEAMSPEAK_MODULE";

	bot::command("", "$MODULE_NAME/teamspeak_server.php", "ts", GUILDMEMBER, "Show Status of the Teamspeak Server");
	
	bot::addsetting("ts_ip", "IP from the TS Server", "edit", "Not set yet.", "text", '0', MODERATOR);	
	bot::addsetting("ts_queryport", "Queryport for the TS Server", "edit", "51234", "number", '0', MODERATOR);
	bot::addsetting("ts_serverport", "Serverport for the TS Server", "edit", "8767", "number", '0', MODERATOR);
	bot::addsetting("ts_servername", "Name of the TS Server", "edit", "Not set yet.", "text", '0', MODERATOR);

	//Help files	
    bot::help("teamspeak", "$MODULE_NAME/ts.txt", GUILDMEMBER, "Using the Teamspeak plugin");
?>
<?php
	$MODULE_NAME = "TEAMSPEAK_MODULE";

	$this->command("", "$MODULE_NAME/teamspeak_server.php", "ts", GUILDMEMBER, "Show Status of the Teamspeak Server");
	
	$this->addsetting("ts_ip", "IP from the TS Server", "edit", "Not set yet.", "text", '0', MODERATOR);	
	$this->addsetting("ts_queryport", "Queryport for the TS Server", "edit", "51234", "number", '0', MODERATOR);
	$this->addsetting("ts_serverport", "Serverport for the TS Server", "edit", "8767", "number", '0', MODERATOR);
	$this->addsetting("ts_servername", "Name of the TS Server", "edit", "Not set yet.", "text", '0', MODERATOR);

	//Help files	
    $this->help("teamspeak", "$MODULE_NAME/ts.txt", GUILDMEMBER, "Using the Teamspeak plugin");
?>
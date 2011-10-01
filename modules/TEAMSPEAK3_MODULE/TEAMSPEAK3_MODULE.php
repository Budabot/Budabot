<?php
	require_once 'Teamspeak3.class.php';

	Command::register($MODULE_NAME, "", "ts.php", "ts", "guild", "Show users connected to Teamspeak3 server");
	Command::register($MODULE_NAME, "", "aospeak.php", "aospeak", "guild", "Show org members connected to AOSpeak server");

	Setting::add($MODULE_NAME, "ts_username", "Username for TS server", "edit", "text", 'serveradmin', 'serveradmin');	
	Setting::add($MODULE_NAME, "ts_password", "Password for TS server", "edit", "text", 'password');
	Setting::add($MODULE_NAME, "ts_queryport", "ServerQuery port for the TS server", "edit", "number", '10011', '10011');
	Setting::add($MODULE_NAME, "ts_clientport", "Client port for the TS server", "edit", "number", '9987', '9987');
	Setting::add($MODULE_NAME, "ts_description", "Description for TS server", "edit", "text", 'Teamspeak 3 Server');
	Setting::add($MODULE_NAME, "ts_server", "IP/Domain name of the TS server", "edit", "text", '127.0.0.1', '127.0.0.1');

	//Help files
	Help::register($MODULE_NAME, "ts", "ts.txt", "guild", "How to use teamspeak");
	Help::register($MODULE_NAME, "aospeak", "aospeak.txt", "guild", "How to use AOSpeak");
?>
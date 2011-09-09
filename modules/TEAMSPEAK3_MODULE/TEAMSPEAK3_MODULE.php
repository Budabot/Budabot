<?php
	require_once 'Teamspeak3.class.php';

	$MODULE_NAME = "TEAMSPEAK3_MODULE";

	Command::register($MODULE_NAME, "", "ts.php", "ts", "guild", "Show users connected to Teamspeak3 server");
	Command::register($MODULE_NAME, "", "aospeak.php", "aospeak", "guild", "Show org members connected to AOSpeak server");

	Setting::add($MODULE_NAME, "ts_username", "Username for TS Server", "edit", "text", 'serveradmin', 'serveradmin');	
	Setting::add($MODULE_NAME, "ts_password", "Password for TS Server", "edit", "text", 'password');
	Setting::add($MODULE_NAME, "ts_port", "Server port for the TS Server", "edit", "number", "10011", '10011');
	Setting::add($MODULE_NAME, "ts_server", "IP/Domain name of the TS Server", "edit", "text", '127.0.0.1', '127.0.0.1');

	//Help files
	Help::register($MODULE_NAME, "ts", "ts.txt", "guild", "How to use teamspeak");
	Help::register($MODULE_NAME, "aospeak", "aospeak.txt", "guild", "How to use AOSpeak");
?>
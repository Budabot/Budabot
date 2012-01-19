<?php
	require_once 'Teamspeak3.class.php';

	$command->register($MODULE_NAME, "", "ts.php", "ts", "guild", "Show users connected to Teamspeak3 server", "ts.txt");
	$command->register($MODULE_NAME, "", "aospeak.php", "aospeak", "all", "Show org members connected to AOSpeak server", "aospeak.txt");
	
	$event->register($MODULE_NAME, "logOn", "send_ts_status.php", "Sends TS status to org members logging on", '', 0);

	$setting->add($MODULE_NAME, "ts_username", "Username for TS server", "edit", "text", 'serveradmin', 'serveradmin');	
	$setting->add($MODULE_NAME, "ts_password", "Password for TS server", "edit", "text", 'password');
	$setting->add($MODULE_NAME, "ts_queryport", "ServerQuery port for the TS server", "edit", "number", '10011', '10011');
	$setting->add($MODULE_NAME, "ts_clientport", "Client port for the TS server", "edit", "number", '9987', '9987');
	$setting->add($MODULE_NAME, "ts_description", "Description for TS server", "edit", "text", 'Teamspeak 3 Server');
	$setting->add($MODULE_NAME, "ts_server", "IP/Domain name of the TS server", "edit", "text", '127.0.0.1', '127.0.0.1');
?>
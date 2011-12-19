<?php
	require_once 'Botconnect.class.php';
	
	DB::loadSQLFile($MODULE_NAME, 'botconnect');

	Event::register($MODULE_NAME, "logOn", "request_invite.php", 'Requests invite from bots on the connect list');
	Event::register($MODULE_NAME, "extJoinPrivRequest", "accept_invite.php", 'Accepts invites from bots on the connect list');
	Event::register($MODULE_NAME, "connect", "connect.php", 'Adds bots on connect list to buddylist');

	Command::register($MODULE_NAME, "", "botconnect.php", "botconnect", "mod", "Show/change the connect list");
	
	Help::register($MODULE_NAME, "botconnect", "botconnect.txt", "all", "How to use botconnect");
?>
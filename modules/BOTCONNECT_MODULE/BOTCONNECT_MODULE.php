<?php
	require_once 'Botconnect.class.php';
	
	DB::loadSQLFile($MODULE_NAME, 'botconnect');

	Event::register($MODULE_NAME, "logOn", "request_invite.php", 'none', 'Requests invite from bots on the connect list');
	Event::register($MODULE_NAME, "extJoinPrivRequest", "accept_invite.php", 'none', 'Accepts invites from bots on the connect list');
	Event::register($MODULE_NAME, "connect", "connect.php", 'none', 'Adds bots on connect list to buddylist');

	Command::register($MODULE_NAME, "", "botconnect.php", "botconnect", "mod", "Show/change the connect list");
	
	Help::register($MODULE_NAME, "botconnect", "botconnect.txt", "all", "How to use botconnect");
?>
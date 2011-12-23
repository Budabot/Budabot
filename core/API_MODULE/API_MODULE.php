<?php
	require_once 'ClientHandler.class.php';
	require_once 'APIRequest.class.php';
	require_once 'APIResponse.class.php';
	
	Event::register($MODULE_NAME, "connect", "connect.php", "Opens a socket to listen for API requests", '', 0);
	Event::register($MODULE_NAME, "2sec", "listen.php", "Checks for and processes API requests", '', 0);
	
	Setting::add($MODULE_NAME, "api_port", "Port number to listen for API requests", "edit", "number", '5250', '5250');

	Command::register($MODULE_NAME, "", "apipassword.php", "apipassword", "mod", "Set your API password");
?>
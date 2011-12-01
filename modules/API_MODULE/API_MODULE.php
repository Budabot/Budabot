<?php
	require_once 'ClientHandler.class.php';
	require_once 'APIRequest.class.php';
	require_once 'APIResponse.class.php';
	
	Event::register($MODULE_NAME, "connect", "connect.php", "", "Opens a socket to listen for API requests");
	Event::register($MODULE_NAME, "2sec", "listen.php", "", "Checks for and processes API requests");
	
	Command::register($MODULE_NAME, "", "testapi.php", "testapi", "mod", "Test API MODULE");

?>
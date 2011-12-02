<?php

/* Accept incoming requests and handle them as child processes */
global $apisocket;
$client = socket_accept($apisocket);
if ($client !== false) {
	$clientHandler = new ClientHandler($client);

	// Read the input from the client
	$apiRequest = $clientHandler->readPacket();
	
	// TODO authentication
	
	switch ($apiRequest->command) {
		case "ping":
			$clientHandler->writePacket(new APIResponse(SUCCESS, "ping", $apiRequest->args));
			break;
		case "restart":
			$clientHandler->writePacket(new APIResponse(SUCCESS, "restart", "Bot is restarting..."));
			exit();
			break;
		case "adminlist":
			$data = $db->query("SELECT * FROM admin_<myname> WHERE adminlevel = 4");
			$clientHandler->writePacket(new APIResponse(SUCCESS, "adminlist", $data));
			break;
		default:
			$clientHandler->writePacket(new APIResponse(FAILURE, "Unknown command: '$apiRequest->command'\n", ""));
	}
}

?>
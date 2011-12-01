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
		case "restart":
			$clientHandler->writePacket(new APIResponse(SUCCESS, "Bot is restarting...", ""));
			exit();
			break;
		default:
			$clientHandler->writePacket(new APIResponse(FAILURE, "Unknown command: '$apiRequest->command'\n", ""));
	}
}

?>
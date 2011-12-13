<?php

/* Accept incoming requests and handle them as child processes */
global $apisocket;
$client = socket_accept($apisocket);
if ($client !== false) {
	$clientHandler = new ClientHandler($client);

	// Read the input from the client
	$apiRequest = $clientHandler->readPacket();
	
	$password = Preferences::get($apiRequest->username, 'apipassword');
	if ($password === false) {
		$clientHandler->writePacket(new APIResponse(API_FAILURE, "Password has not been set for this user."));
	} else if ($password != $apiRequest->password) {
		$clientHandler->writePacket(new APIResponse(API_FAILURE, "Password was incorrect."));
	} else {
		$chatBot->process_command('api', $apiRequest->command, $apiRequest->username, $clientHandler);
	}
}

?>
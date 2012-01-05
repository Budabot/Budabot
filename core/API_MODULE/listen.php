<?php

/* Accept incoming requests and handle them as child processes */
global $apisocket;
$client = socket_accept($apisocket);
if ($client !== false) {
	$clientHandler = new ClientHandler($client);

	// Read the input from the client
	$apiRequest = $clientHandler->readPacket();
	if ($apiRequest->version != API_VERSION) {
		$clientHandler->writePacket(new APIResponse(API_FAILURE, "API version must be: " . API_VERSION));
	}
	
	$password = Preferences::get($apiRequest->username, 'apipassword');
	if ($password === false) {
		$clientHandler->writePacket(new APIResponse(API_FAILURE, "Password has not been set for this user."));
	} else if ($password != $apiRequest->password) {
		$clientHandler->writePacket(new APIResponse(API_FAILURE, "Password was incorrect."));
	} else {
		$type = 'msg';
		if ($apiRequest->type == API_ADVANCED_MSG) {
			$type = 'api';
		}
		$command = Registry::getInstance('command');
		$command->command($type, $apiRequest->command, $apiRequest->username, $clientHandler);
	}
}

?>
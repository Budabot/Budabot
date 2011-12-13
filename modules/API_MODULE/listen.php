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
		$clientHandler->writePacket(new APIResponse(FAILURE, "login", "Password has not been set for this user."));
	} else if ($password != $apiRequest->password) {
		$clientHandler->writePacket(new APIResponse(FAILURE, "login", "Password was incorrect."));
	} else if (!AccessLevel::checkAccess($apiRequest->username, 'mod')) {
		$clientHandler->writePacket(new APIResponse(FAILURE, "login", "You must have moderator access or higher to use this feature."));
	} else {
		$chatBot->process_command('api', $apiRequest->command, $apiRequest->username, $clientHandler);
	}
}

?>
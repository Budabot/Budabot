<?php

global $apisocket;

// bind to port 5250 on any address
$address = '0.0.0.0';
$port = Setting::get('api_port');

// Create a TCP Stream socket
$apisocket = socket_create(AF_INET, SOCK_STREAM, 0);
socket_bind($apisocket, $address, $port);
$errno = socket_last_error();
if ($errno == 0) {
	Logger::log('INFO', 'API_MODULE', 'API socket bound successfully');
} else {
	Logger::log('ERROR', 'API_MODULE', socket_strerror($errno));
}
socket_listen($apisocket);
socket_set_nonblock($apisocket);

?>
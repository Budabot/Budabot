<?php

global $apisocket;

// Set the ip and port we will listen on
$address = '127.0.0.1';
$port = 9000;

// Create a TCP Stream socket
$apisocket = socket_create(AF_INET, SOCK_STREAM, 0);
socket_bind($apisocket, $address, $port) or die('Could not bind to address');
socket_listen($apisocket);
socket_set_nonblock($apisocket);

?>
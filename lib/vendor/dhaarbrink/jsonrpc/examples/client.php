<pre>
<?php

/*

This example shows the basic usage of the RpcClient
Examples are shown for normal and batched mode


 */

	//just in case, show all errors we've got
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    //get us some autoloading
    require_once __DIR__ . '/../vendor/autoload.php';

    //construct the server url and instantiate the client
    $url = 'http://' . $_SERVER['SERVER_ADDR'] . dirname($_SERVER['REQUEST_URI']) . '/server.php';
    $client = new JsonRpc\RpcClient($url);

    //basic method call in normal mode
    $result = $client->testfunc();
    var_dump($result, $client->getResponseRaw());
    
    
    //multiple methods in a batch call
    //remember, the server is free to process the requests in any order
    //it wants
    $result = $client->batch()
    	->testfunc()
    	->testfunc2()
    	->send();
	var_dump($result, $client->getResponseRaw());
	
	
	//example of a method that throws an exception
	$result = $client->throwsException('the param');
	var_dump($result, $client->getResponseRaw());

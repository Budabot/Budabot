<?php

if (preg_match("/^testapi (.*)$/i", $message, $arr)) {
	$command = $arr[1];
	
	$apiRequest = new APIRequest($command, "");
	$socket = fsockopen("127.0.0.1", 5250);
	
	$output = json_encode($apiRequest);
	fputs($socket, pack("n", strlen($output)));
	fputs($socket, $output);
	$size = array_pop(unpack("n", fread($socket, 2)));
	$obj = json_decode(fread($socket, $size));
	fclose($socket);
	
	$chatBot->send($obj->args, $sendto);
} else {
	$syntax_error = true;
}

?>
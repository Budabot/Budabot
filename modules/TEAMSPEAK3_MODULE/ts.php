<?php

$msg = "test";
if (preg_match("/^ts$/i", $message)) {
	// The example
	///////////////
	$username = "serveradmin";
	$password = "FdddLBZN";
	$ts =  new Teamspeak3($username, $password, "69.60.115.210");

	$onlinePlayers = $ts->exec('clientlist');
	print_r($onlinePlayers);

	$playerInfo = $ts->exec('clientinfo clid=4');
	print_r($playerInfo);
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
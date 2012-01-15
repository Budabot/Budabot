<?php

if (preg_match("/^countdown$/i", $message) || preg_match("/^countdown (.+)$/i", $message, $arr)) {
  	global $countdown_last;
	
	$message = "GO GO GO";
	if (isset($arr)) {
		$message = $arr[1];
	}
  	
  	if ($countdown_last >= (time() - 30)) {
		$msg = "<red>You can only start a countdown every 30 seconds!<end>";
	    $chatBot->send($msg, $sendto);
	    return;
	}
	
	$countdown_last = time();
	
	for ($i = 5; $i > 3; $i--) {
		$msg = "<red>-------> $i <-------<end>";
	    $chatBot->send($msg, $sendto);
	    sleep(1);
	}

	for ($i = 3; $i > 0; $i--) {
		$msg = "<orange>-------> $i <-------<end>";
	    $chatBot->send($msg, $sendto);
	    sleep(1);
	}

	$msg = "<green>-------> $message <-------<end>";
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
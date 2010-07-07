<?php
if (preg_match("/^countdown$/i", $message)) {
  	global $countdown_last;
  	
  	if($countdown_last >= (time() - 30)) {
		$msg = "<red>You can only start a countdown every 30seconds!<end>";
	    // Send info back
	    bot::send($msg, $sendto);
	    return;
	}
	
	$countdown_last = time();
	
	for($i = 5; $i > 3; $i--) {
		$msg = "<red>-------> $i <-------<end>";
	    // Send info back
	    bot::send($msg, $sendto);
	    sleep(1);
	}

	for($i = 3; $i > 0; $i--) {
		$msg = "<orange>-------> $i <-------<end>";
	    // Send info back
	    bot::send($msg, $sendto);
	    sleep(1);
	}

	$msg = "<green>-------> GO GO GO <-------<end>";
    // Send info back
    bot::send($msg, $sendto);
} else
	$syntax_error = true;
?>
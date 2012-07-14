<?php

if (preg_match("/^countdown$/i", $message) || preg_match("/^countdown (.+)$/i", $message, $arr)) {
	global $countdown_last;

	$message = "GO GO GO";
	if (isset($arr)) {
		$message = $arr[1];
	}

	if ($countdown_last >= (time() - 30)) {
		$msg = "<red>You can only start a countdown every 30 seconds!<end>";
	    $sendto->reply($msg);
	    return;
	}

	$countdown_last = time();

	for ($i = 5; $i > 3; $i--) {
		$msg = "<red>-------> $i <-------<end>";
	    $sendto->reply($msg);
	    sleep(1);
	}

	for ($i = 3; $i > 0; $i--) {
		$msg = "<orange>-------> $i <-------<end>";
	    $sendto->reply($msg);
	    sleep(1);
	}

	$msg = "<green>-------> $message <-------<end>";
    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>

<?
if(eregi("^countdown$", $message)) {
  	global $countdown_last;
  	
  	if($countdown_last >= (time() - 30)) {
		$msg = "<red>You can only start a countdown every 30seconds!<end>";
	    // Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	      	bot::send($msg);
	    elseif($type == "guild")
	      	bot::send($msg, "guild");
	    return;
	}
	
	$countdown_last = time();
	
	for($i = 5; $i > 3; $i--) {
		$msg = "<red>-------> $i <-------<end>";
	    // Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	      	bot::send($msg);
	    elseif($type == "guild")
	      	bot::send($msg, "guild");      	
	    sleep(1);
	}

	for($i = 3; $i > 0; $i--) {
		$msg = "<orange>-------> $i <-------<end>";
	    // Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	      	bot::send($msg);
	    elseif($type == "guild")
	      	bot::send($msg, "guild");      	
	    sleep(1);
	}

	$msg = "<green>-------> GO GO GO <-------<end>";
    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
      	bot::send($msg);
    elseif($type == "guild")
      	bot::send($msg, "guild");      	
} else
	$syntax_error = true;
?>
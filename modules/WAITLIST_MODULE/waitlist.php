<?php
   
global $waitlist;
if (preg_match("/^waitlist next$/i", $message)) {
	if (count($waitlist[$sender]) == 0) {
	  	$msg = "There is no one on your waitlist!";
	    $chatBot->send($msg, $sendto);
      	return;
	}
	
	$name = array_shift(array_keys($waitlist[$sender]));
	unset($waitlist[$sender][$name]);
	$waitlist[$sender][$name] = true;
	$chatBot->send("<highlight>$sender waitlist<end>: You can come now!", $name);

	$msg = "<highlight>$name<end> has been called to come now.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist add (.+)$/i", $message, $arr)) {
  	$uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
      	$msg = "Player <highlight>".$name."<end> does not exist.";
		$chatBot->send($msg, $sendto);
	    return;
    }
	
	if (isset($waitlist[$sender][$name])) {
	  	$msg = "<highlight>$name<end> is already on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}
	
	$waitlist[$sender][$name] = true;
	$msg = "<highlight>$name<end> has been added to your waitlist.";
	$chatBot->send($msg, $sendto);
	  	
	$chatBot->send("You have been added to the waitlist of <highlight>$sender<end>.", $name);
} else if (preg_match("/^waitlist rem all$/i", $message)) {
  	if (count($waitlist[$sender]) == 0) {
	  	$msg = "There is no one on your waitlist atm!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}

	unset($waitlist[$sender]);
	
	$msg = "Your waitlist has been cleared.";
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist rem (.+)$/i", $message, $arr)) {
  	$uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
      	$msg = "Player <highlight>".$name."<end> does not exist.";
   	    $chatBot->send($msg, $sendto);
      	return;
    }
	
	if (!isset($waitlist[$sender][$name])) {
	  	$msg = "<highlight>$name<end> is not on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}

	unset($waitlist[$sender][$name]);
	$msg = "You have been removed from {$sendto}'s waitlist.";
	$chatBot->send($msg, $name);
	
	$msg = "<highlight>$name<end> has been removed from your waitlist.";
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist$/i", $message) || preg_match("/^waitlist ([a-z0-9-]+)$/i", $message, $arr)) {
	if ($arr) {
		$char = ucfirst(strtolower($arr[1]));
	} else {
		$char = $sender;
	}

  	if (count($waitlist[$char]) == 0) {
	 	$msg = "<highlight>$char<end> doesn't have a waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}
	
	$count = 0;
	$blob = "<header> :::::: Waitlist for $char :::::: <end>\n\n";
	forEach($waitlist[$char] as $name => $value) {
		$count++;
		$blob .= "{$count}. $name \n";
	}
	
	$msg = Text::make_link("Waitlist for $char ($count)", $blob, 'blob');

  	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
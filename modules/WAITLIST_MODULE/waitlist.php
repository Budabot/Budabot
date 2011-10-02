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
    $name = ucfirst(strtolower($arr[1]));
	
	if (isset($waitlist[$sender][$name])) {
	  	$msg = "<highlight>$name<end> is already on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}
	
	$waitlist[$sender][$name] = true;
	$msg = "<highlight>$name<end> has been added to your waitlist.";
	$chatBot->send($msg, $sendto);
	  	
	$chatBot->send("You have been added to the waitlist of <highlight>$sender<end>.", $name);
} else if (preg_match("/^waitlist (rem all|clear)$/i", $message)) {
  	if (count($waitlist[$sender]) == 0) {
	  	$msg = "There is no one on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}

	unset($waitlist[$sender]);
	
	$msg = "Your waitlist has been cleared.";
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist rem (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
	
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
} else if (preg_match("/^waitlist shuffle$/i", $message)) {
  	if (count($waitlist[$sender]) == 0) {
	  	$msg = "There is no one on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}

	$keys = array_keys($waitlist[$sender]);
	shuffle($keys);
	$random = array();
	foreach ($keys as $key) {
		$random[$key] = $waitlist[$sender][$key];
	}
	$waitlist[$sender] = $random;
	
	$count = 0;
	$blob = "<header> :::::: Waitlist for $sender :::::: <end>\n\n";
	forEach ($waitlist[$sender] as $name => $value) {
		$count++;
		$blob .= "{$count}. $name \n";
	}
	
	$msg = "Your waitlist has been shuffled. " . Text::make_blob("Waitlist for $sender ($count)", $blob);
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist$/i", $message) || preg_match("/^waitlist ([a-z0-9-]+)$/i", $message, $arr)) {
	if (isset($arr)) {
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
	forEach ($waitlist[$char] as $name => $value) {
		$count++;
		$blob .= "{$count}. $name \n";
	}
	
	$msg = Text::make_blob("Waitlist for $char ($count)", $blob);

  	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
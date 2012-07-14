<?php

global $waitlist;
if (preg_match("/^waitlist next$/i", $message)) {
	if (count($waitlist[$sender]) == 0) {
		$msg = "There is no one on your waitlist!";
	    $sendto->reply($msg);
	return;
	}

	$name = array_shift(array_keys($waitlist[$sender]));
	unset($waitlist[$sender][$name]);
	$waitlist[$sender][$name] = true;
	$chatBot->sendTell("<highlight>$sender waitlist<end>: You can come now!", $name);

	$msg = "<highlight>$name<end> has been called to come now.";
	$sendto->reply($msg);
} else if (preg_match("/^waitlist add (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));

	if (isset($waitlist[$sender][$name])) {
		$msg = "<highlight>$name<end> is already on your waitlist!";
		$sendto->reply($msg);
	return;
	}

	$waitlist[$sender][$name] = true;
	$chatBot->sendTell("You have been added to the waitlist of <highlight>$sender<end>.", $name);

	$msg = "<highlight>$name<end> has been added to your waitlist.";
	$sendto->reply($msg);
} else if (preg_match("/^waitlist (rem all|clear)$/i", $message)) {
	if (count($waitlist[$sender]) == 0) {
		$msg = "There is no one on your waitlist!";
		$sendto->reply($msg);
	return;
	}

	unset($waitlist[$sender]);

	$msg = "Your waitlist has been cleared.";
    $sendto->reply($msg);
} else if (preg_match("/^waitlist rem (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));

	if (!isset($waitlist[$sender][$name])) {
		$msg = "<highlight>$name<end> is not on your waitlist!";
		$sendto->reply($msg);
	return;
	}

	unset($waitlist[$sender][$name]);
	$msg = "You have been removed from {$sendto}'s waitlist.";
	$chatBot->sendTell($msg, $name);

	$msg = "<highlight>$name<end> has been removed from your waitlist.";
    $sendto->reply($msg);
} else if (preg_match("/^waitlist shuffle$/i", $message)) {
	if (count($waitlist[$sender]) == 0) {
		$msg = "There is no one on your waitlist!";
		$sendto->reply($msg);
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
	$blob = '';
	forEach ($waitlist[$sender] as $name => $value) {
		$count++;
		$blob .= "{$count}. $name \n";
	}

	$msg = "Your waitlist has been shuffled. " . Text::make_blob("Waitlist for $sender ($count)", $blob);
    $sendto->reply($msg);
} else if (preg_match("/^waitlist$/i", $message) || preg_match("/^waitlist ([a-z0-9-]+)$/i", $message, $arr2)) {
	if (isset($arr2)) {
		$char = ucfirst(strtolower($arr[1]));
	} else {
		$char = $sender;
	}

	if (count($waitlist[$char]) == 0) {
		$msg = "<highlight>$char<end> doesn't have a waitlist!";
		$sendto->reply($msg);
	return;
	}

	$count = 0;
	$blob = '';
	forEach ($waitlist[$char] as $name => $value) {
		$count++;
		$blob .= "{$count}. $name \n";
	}

	$msg = Text::make_blob("Waitlist for $char ($count)", $blob);

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>

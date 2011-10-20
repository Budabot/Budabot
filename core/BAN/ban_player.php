<?php

if (preg_match("/^ban (.+) ([a-z0-9]+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[4];

	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("The character you wish to ban does not exist.", $sendto);
		return;
	}
	
	if (Ban::is_banned($who)) {
	  	$chatBot->send("The character <highlight$who<end> is already banned.", $sendto);
		return;
	}
	
	$length = Util::parseTime($arr[2]);
	if ($length == 0) {
		$msg = "Your timer must be longer than 0 seconds.";
		$chatBot->send($msg, $sendto);
		return;
	}
	$timeString = Util::unixtime_to_readable($length);

	Ban::add($who, $sender, $length, $reason);

	$chatBot->send("You have banned <highlight>$who<end> from this bot for $timeString.", $sendto);
	if (Setting::get('notify_banned_player') == 1) {
		$chatBot->send("You have been banned from this bot by <highlight>$sender<end> for $timeString. Reason: $reason", $who);
	}
} else if (preg_match("/^ban (.+) ([a-z0-9]+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("The character you wish to ban does not exist.", $sendto);
		return;
	}
	
	if (Ban::is_banned($who)) {
	  	$chatBot->send("The character <highlight>$who<end> is already banned.", $sendto);
		return;
	}
	
	$length = Util::parseTime($arr[2]);
	if ($length == 0) {
		$msg = "Your timer must be longer than 0 seconds.";
		$chatBot->send($msg, $sendto);
		return;
	}
	$timeString = Util::unixtime_to_readable($length);
	
	Ban::add($who, $sender, $length, '');

	if (Setting::get('notify_banned_player') == 1) {
		$chatBot->send("You have banned <highlight>$who<end> from this bot for $timeString.", $sendto);
	}
	$chatBot->send("You have been banned from this bot by <highlight>$sender<end> for $timeString.", $who);
} else if (preg_match("/^ban (.+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[3];
	
	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("The character you wish to ban does not exist.", $sendto);
		return;
	}

	if (Ban::is_banned($who)) {
	  	$chatBot->send("The character <highlight>$who<end> is already banned.", $sendto);
		return;
	}
		
	Ban::add($who, $sender, null, $reason);

	if (Setting::get('notify_banned_player') == 1) {
		$chatBot->send("You have permanently banned <highlight>$who<end> from this bot.", $sendto);
	}
	$chatBot->send("You have been permanently banned from this bot by <highlight>$sender<end>. Reason: $reason", $who);
} else if (preg_match("/^ban (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("The character you wish to ban does not exist.", $sendto);
		return;
	}

	if (Ban::is_banned($who)) {
	  	$chatBot->send("The character <highlight>$who<end> is already banned.", $sendto);
		return;
	}
	
	Ban::add($who, $sender, null, '');

	if (Setting::get('notify_banned_player') == 1) {
		$chatBot->send("You have permanently banned <highlight>$who<end> from this bot.", $sendto);
	}
	$chatBot->send("You have been permanently banned from this bot by <highlight>$sender<end>.", $who);
} else if (preg_match("/^banorg (.+)$/i", $message, $arr)) {
	$who = $arr[1];
	
	if (Ban::is_banned($who)) {
	  	$chatBot->send("The organization <highlight>$who<end> is already banned.", $sendto);
		return;
	}
	
	Ban::add($who, $sender, null, '');

	$chatBot->send("You have banned the organization <highlight>$who<end> from this bot.", $sendto);
} else {
	$syntax_error = true;
}

?>
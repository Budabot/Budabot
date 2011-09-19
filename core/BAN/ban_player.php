<?php

if (preg_match("/^ban (.+) ([a-z0-9]+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[4];

	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("<red>Sorry the player you wish to ban does not exist.", $sendto);
		return;
	}
	
	if (Ban::is_banned($who)) {
	  	$chatBot->send("<red>Player $who is already banned.<end>", $sendto);
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
	$chatBot->send("You have been banned from this bot by $sender for $timeString. Reason: $reason", $who);
} else if (preg_match("/^ban (.+) ([a-z0-9]+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("<red>Sorry the player you wish to ban does not exist.", $sendto);
		return;
	}
	
	if (Ban::is_banned($who)) {
	  	$chatBot->send("<red>Player $who is already banned.<end>", $sendto);
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

	$chatBot->send("You have banned <highlight>$who<end> from this bot for $timeString.", $sendto);
	$chatBot->send("You have been banned from this bot by $sender for $timeString.", $who);
} else if (preg_match("/^ban (.+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[3];
	
	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("<red>Sorry player you wish to ban does not exist.", $sendto);
		return;
	}

	if (Ban::is_banned($who)) {
	  	$chatBot->send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
		
	Ban::add($who, $sender, null, $reason);

	$chatBot->send("You have permanently banned <highlight>$who<end> from this bot.", $sendto);
	$chatBot->send("You have been permanently banned from this bot by $sender. Reason: $reason", $who);
} else if (preg_match("/^ban (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL) {
		$chatBot->send("<red>Sorry player you wish to ban does not exist.", $sendto);
		return;
	}

	if (Ban::is_banned($who)) {
	  	$chatBot->send("<red>Player $who is already banned.<end>", $sendto);
		return;
	}
	
	Ban::add($who, $sender, null, '');

	$chatBot->send("You have permanently banned <highlight>$who<end> from this bot.", $sendto);
	$chatBot->send("You have been permanently banned from this bot by $sender.", $who);
} else if (preg_match("/^banorg (.+)$/i", $message, $arr)) {
	$who = $arr[1];
	
	if (Ban::is_banned($who)) {
	  	$chatBot->send("<red>The organization $who is already banned.<end>", $sendto);
		return;
	}
	
	Ban::add($who, $sender, null, '');

	$chatBot->send("You have banned the org <highlight>$who<end> from this bot.", $sendto);
} else {
	$syntax_error = true;
}

?>
<?php

$ban = Registry::getInstance('ban');
$accessLevel = Registry::getInstance('accessLevel');

if (preg_match("/^ban (.+) ([a-z0-9]+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[4];

	if ($chatBot->get_uid($who) == NULL) {
		$sendto->reply("Character <highlight$who<end> does not exist.");
		return;
	}
	
	if ($ban->is_banned($who)) {
	  	$sendto->reply("Character <highlight$who<end> is already banned.");
		return;
	}
	
	if ($accessLevel->compareCharacterAccessLevels($sender, $who) <= 0) {
		$sendto->reply("You must have a higher access level than $who to perform this function.");
		return;
	}
	
	$length = Util::parseTime($arr[2]);
	if ($length == 0) {
		$msg = "Your timer must be longer than 0 seconds.";
		$sendto->reply($msg);
		return;
	}
	$timeString = Util::unixtime_to_readable($length);

	$ban->add($who, $sender, $length, $reason);

	$sendto->reply("You have banned <highlight>$who<end> from this bot for $timeString.");
	if ($setting->get('notify_banned_player') == 1) {
		$chatBot->sendTell("You have been banned from this bot by <highlight>$sender<end> for $timeString. Reason: $reason", $who);
	}
} else if (preg_match("/^ban (.+) ([a-z0-9]+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL) {
		$sendto->reply("Character <highlight$who<end> does not exist.");
		return;
	}
	
	if ($ban->is_banned($who)) {
	  	$sendto->reply("Character <highlight>$who<end> is already banned.");
		return;
	}
	
	if ($accessLevel->compareCharacterAccessLevels($sender, $who) <= 0) {
		$sendto->reply("You must have a higher access level than $who to perform this function.");
		return;
	}
	
	$length = Util::parseTime($arr[2]);
	if ($length == 0) {
		$msg = "Your timer must be longer than 0 seconds.";
		$sendto->reply($msg);
		return;
	}
	$timeString = Util::unixtime_to_readable($length);
	
	$ban->add($who, $sender, $length, '');

	if ($setting->get('notify_banned_player') == 1) {
		$sendto->reply("You have banned <highlight>$who<end> from this bot for $timeString.");
	}
	$chatBot->sendTell("You have been banned from this bot by <highlight>$sender<end> for $timeString.", $who);
} else if (preg_match("/^ban (.+) (for|reason) (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	$reason = $arr[3];
	
	if ($chatBot->get_uid($who) == NULL) {
		$sendto->reply("Character <highlight$who<end> does not exist.");
		return;
	}

	if ($ban->is_banned($who)) {
	  	$sendto->reply("Character <highlight>$who<end> is already banned.");
		return;
	}
	
	if ($accessLevel->compareCharacterAccessLevels($sender, $who) <= 0) {
		$sendto->reply("You must have a higher access level than $who to perform this function.");
		return;
	}
		
	$ban->add($who, $sender, null, $reason);

	if ($setting->get('notify_banned_player') == 1) {
		$sendto->reply("You have permanently banned <highlight>$who<end> from this bot.");
	}
	$chatBot->sendTell("You have been permanently banned from this bot by <highlight>$sender<end>. Reason: $reason", $who);
} else if (preg_match("/^ban (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL) {
		$sendto->reply("Character <highlight$who<end> does not exist.");
		return;
	}

	if ($ban->is_banned($who)) {
	  	$sendto->reply("Character <highlight>$who<end> is already banned.");
		return;
	}
	
	if ($accessLevel->compareCharacterAccessLevels($sender, $who) <= 0) {
		$sendto->reply("You must have a higher access level than $who to perform this function.");
		return;
	}
	
	$ban->add($who, $sender, null, '');

	if ($setting->get('notify_banned_player') == 1) {
		$sendto->reply("You have permanently banned <highlight>$who<end> from this bot.");
	}
	$chatBot->sendTell("You have been permanently banned from this bot by <highlight>$sender<end>.", $who);
} else if (preg_match("/^banorg (.+)$/i", $message, $arr)) {
	$who = $arr[1];
	
	if ($ban->is_banned($who)) {
	  	$sendto->reply("The organization <highlight>$who<end> is already banned.");
		return;
	}
	
	$ban->add($who, $sender, null, '');

	$sendto->reply("You have banned the organization <highlight>$who<end> from this bot.");
} else {
	$syntax_error = true;
}

?>
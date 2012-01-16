<?php

if (preg_match("/^aospeak$/i", $message) || preg_match("/^aospeak org$/i", $message)) {
	$url = "http://api.aospeak.com/org/" . $chatBot->vars['dimension'] . "/" . $chatBot->vars['my_guild_id'];
	$results = file_get_contents($url);
	
	if ($results == "ORG_NOT_FOUND") {
		$msg = "Your org is not currently set up on AOSpeak. Please have your org president set up a channel first.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$users = json_decode($results);
	$count = count($users);
	if ($count == 0) {
		$msg = "No org members currently connected to AOSpeak.";
	} else {
		$blob = "Server: <highlight>voice.aospeak.com<end>";
		$blob .= "\n\nUsers:\n";
		forEach ($users as $user) {
			if ($user->idleTime >= 300000) {
				// if idle for at least 5 minutes
				$blob .= "<highlight>{$user->name}<end> ({$user->country}, idle for " . Util::unixtime_to_readable($user->idleTime / 1000, false) . ")\n";
			} else {
				$blob .= "<highlight>{$user->name}<end> ({$user->country})\n";
			}
		}
		$blob .= "\n\nProvided by " . Text::make_chatcmd("AOSpeak.com", "/start http://www.aospeak.com");
		$msg = Text::make_blob("AOSpeak Org ($count)", $blob);
	}
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^aospeak all$/i", $message) || preg_match("/^aospeak (\\d)$/i", $message, $arr) || preg_match("/^aospeak (\\d)$/i", $message, $arr)) {
	if (isset($arr)) {
		$title = "AOSpeak Online RK" . $arr[1];
		$url = "http://api.aospeak.com/online/" . $arr[1];
	} else {
		$title = "AOSpeak Online";
		$url = "http://api.aospeak.com/online/";
	}
	$results = file_get_contents($url);
	
	$users = json_decode($results);
	$count = count($users);
	if ($count == 0) {
		$msg = "No players currently connected to AOSpeak.";
	} else {
		$blob = "Server: <highlight>voice.aospeak.com<end>\n";
		
		$channels = array();
		forEach ($users as $user) {
			$channels[$user->channelName] []= $user;
		}
		
		forEach ($channels as $name => $users) {
			$blob .= "\n<green>$name<end>\n";
			forEach ($users as $user) {
				if ($user->idleTime >= 300000) {
					// if idle for at least 5 minutes
					$blob .= "<tab><highlight>{$user->name}<end> (RK{$user->dim}, {$user->country}, idle for " . Util::unixtime_to_readable($user->idleTime / 1000, false) . ")\n";
				} else {
					$blob .= "<tab><highlight>{$user->name}<end> (RK{$user->dim}, {$user->country})\n";
				}
			}
		}
		$blob .= "\n\nProvided by " . Text::make_chatcmd("AOSpeak.com", "/start http://www.aospeak.com");
		$msg = Text::make_blob("$title ($count)", $blob);
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
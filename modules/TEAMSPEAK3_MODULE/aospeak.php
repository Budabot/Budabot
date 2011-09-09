<?php

if (preg_match("/^aospeak$/i", $message)) {
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
		$blob = "<header> :::::: Org Members on AOSpeak :::::: <end>\n\n";
		$blob .= "Server: <highlight>voice.aospeak.com<end>";
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
		$msg = Text::make_blob("AOSpeak ($count)", $blob);
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
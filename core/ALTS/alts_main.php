<?php

if (preg_match("/^alts main ([a-z0-9-]+)$/i", $message, $arr)) {
	$new_main = Alts::get_alt_info($arr[1])->main;

	$uid = $chatBot->get_uid($new_main);
	if (!$uid) {
		$msg = "The character <highlight>$new_main<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$altInfo = Alts::get_alt_info($sender);
	
	if ($altInfo->main == $new_main) {
		$msg = "You are already registered as an alt of <highlight>{$new_main}<end>.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	if ($altInfo->main == $sender && count($altInfo->alts) > 0) {
		$msg = "You must not have any alts already registered.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	// let them know if they are changing the main for this character
	if ($altInfo->main != $sender) {
		Alts::rem_alt($altInfo->main, $sender);
		$msg = "You have been removed as an alt of <highlight>{$altInfo->main}<end>.";
		$chatBot->send($msg, $sendto);
	}

	Alts::add_alt($new_main, $sender, 0);
	$msg = "You have been registered as an alt of <highlight>{$new_main}<end>.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

<?php

if (preg_match("/^alts main ([a-z0-9-]+)$/i", $message, $arr)) {
	$new_main = Alts::get_alt_info($arr[1])->main;

	$uid = $chatBot->get_uid($new_main);
	if (!$uid) {
		$msg = "Character <highlight>$new_main<end> does not exist.";
		$sendto->reply($msg);
		return;
	}
	
	$altInfo = Alts::get_alt_info($sender);
	
	if ($altInfo->main == $new_main) {
		$msg = "You are already registered as an alt of <highlight>{$new_main}<end>.";
		$sendto->reply($msg);
		return;
	}
	
	if ($altInfo->main == $sender && count($altInfo->alts) > 0) {
		$msg = "You must not have any alts already registered.";
		$sendto->reply($msg);
		return;
	}
	
	// let them know if they are changing the main for this character
	if ($altInfo->main != $sender) {
		Alts::rem_alt($altInfo->main, $sender);
		$msg = "You have been removed as an alt of <highlight>{$altInfo->main}<end>.";
		$sendto->reply($msg);
	}

	Alts::add_alt($new_main, $sender, 0);
	$msg = "You have been registered as an alt of <highlight>{$new_main}<end>.";
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>

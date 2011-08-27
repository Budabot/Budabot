<?php

if (preg_match("/^alts add ([a-z0-9- ]+)$/i", $message, $arr)) {
	/* get all names in an array */
	$names = explode(' ', $arr[1]);
	
	$sender = ucfirst(strtolower($sender));
	
	$senderAltInfo = Alts::get_alt_info($sender);
	$main = $senderAltInfo->main;
	
	/* Pop a name from the array until none are left (checking for null) */
	foreach ($names as $name) {
		$name = ucfirst(strtolower($name));
		
		if ($name == $sender) {
			$msg = "You cannot register yourself as your alt.";
			$chatBot->send($msg, $sendto);
			continue;
		}
		
		$altInfo = Alts::get_alt_info($name);
		if ($altInfo->main == $senderAltInfo->main) {
			// Already registered to self
			$msg = "$name is already registered to you.";
			$chatBot->send($msg, $sendto);
			continue;
		}
		
		if (count($altInfo->alts) > 0) {
			// Already registered to someone else
			if ($altInfo->main == $name) {
				$msg = "$name is already registered as a main with alts.";
			} else {
				$msg = "$name is already registered as an of alt of {$altInfo->main}.";
			}
			$chatBot->send($msg, $sendto);
			continue;
		}
		
		$validated = 0;
		
		if ($sender == $senderAltInfo->main || (Setting::get("validate_from_validated_alt") == 1 && $senderAltInfo->is_validated($sender))) {
			$validated = 1;
		}
		
		/* insert into database */
		Alts::add_alt($senderAltInfo->main, $name, $validated);
		$msg = "$name was successfully registered as your alt.";
		$chatBot->send($msg, $sendto);
		
		// update character info
		Player::get_by_name($name);
	}
} else if (preg_match("/^alts (rem|del|remove|delete) ([a-z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));
	
	$altInfo = Alts::get_alt_info($sender);
	
	if (!array_key_exists($name, $altInfo->alts)) {
		$msg = "<highlight>{$name}<end> is not registered as your alt.";
	} else {
		Alts::rem_alt($altInfo->main, $name);
		$msg = "<highlight>{$name}<end> has been deleted from your alt list.";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match('/^alts setmain ([a-z0-9-]+)$/i', $message, $arr)) {
	// check if new main exists
	$new_main = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($new_main);
	if (!$uid) {
		$msg = "Player <highlight>{$new_main}<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$altInfo = Alts::get_alt_info($sender);
	
	if (!array_key_exists($new_main, $altInfo->alts)) {
		$msg = "<highlight>{$new_main}<end> must first be registered as your alt.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$db->beginTransaction();

	// remove all the old alt information
	$db->exec("DELETE FROM `alts` WHERE `main` = '{$altInfo->main}'");

	// add current main to new main as an alt
	Alts::add_alt($new_main, $altinfo->main);
	
	// add current alts to new main
	forEach ($altInfo->alts as $alt => $validated) {
		if ($alt != $new_main) {
			Alts::add_alt($new_main, $alt);
		}
	}
	
	$db->commit();

	$msg = "Successfully set your new main as <highlight>{$new_main}<end>.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^alts ([a-z0-9-]+)$/i", $message, $arr) || preg_match("/^alts$/i", $message, $arr)) {
	if (isset($arr[1])) {
		$name = ucfirst(strtolower($arr[1]));
	} else {
		$name = $sender;
	}

	$msg = Alts::get_alts_blob($name);
	
	if ($msg === null) {
		$msg = "No alts are registered for <highlight>{$name}<end>.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

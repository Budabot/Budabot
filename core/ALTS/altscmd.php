<?php

if (preg_match("/^alts add ([a-z0-9- ]+)$/i", $message, $arr)) {
	/* get all names in an array */
	$names = explode(' ', $arr[1]);
	
	$sender = ucfirst(strtolower($sender));
	
	$senderAltInfo = Alts::get_alt_info($sender);
	$main = $senderAltInfo->main;
	
	$success = 0;
	
	/* Pop a name from the array until none are left (checking for null) */
	forEach ($names as $name) {
		$name = ucfirst(strtolower($name));
		
		$uid = $chatBot->get_uid($name);
		if (!$uid) {
			$msg = "Character <highlight>{$name}<end> does not exist.";
			$sendto->reply($msg);
			continue;
		}
		
		$altInfo = Alts::get_alt_info($name);
		if ($altInfo->main == $senderAltInfo->main) {
			// already registered to self
			$msg = "<highlight>$name<end> is already registered to you.";
			$sendto->reply($msg);
			continue;
		}
		
		if (count($altInfo->alts) > 0) {
			// already registered to someone else
			if ($altInfo->main == $name) {
				$msg = "<highlight>$name<end> is already registered as a main with alts.";
			} else {
				$msg = "<highlight>$name<end> is already registered as an of alt of <highlight>{$altInfo->main}<end>.";
			}
			$sendto->reply($msg);
			continue;
		}
		
		$validated = 0;
		if ($sender == $senderAltInfo->main || ($setting->get("validate_from_validated_alt") == 1 && $senderAltInfo->is_validated($sender))) {
			$validated = 1;
		}
		
		/* insert into database */
		Alts::add_alt($senderAltInfo->main, $name, $validated);
		$success++;
		
		// update character information
		Player::get_by_name($name);
	}
	
	if ($success > 0) {
		$msg = ($success == 1 ? "Alt" : "$success alts") . " added successfully.";
		$sendto->reply($msg);
	}
} else if (preg_match("/^alts (rem|del|remove|delete) ([a-z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));
	
	$altInfo = Alts::get_alt_info($sender);
	
	if (!array_key_exists($name, $altInfo->alts)) {
		$msg = "<highlight>{$name}<end> is not registered as your alt.";
	} else if (!$altInfo->is_validated($sender) && $altInfo->is_validated($name)) {
		$msg = "You must be on a validated alt to remove another alt that is validated.";
	} else {
		Alts::rem_alt($altInfo->main, $name);
		$msg = "<highlight>{$name}<end> has been deleted from your alt list.";
	}
	$sendto->reply($msg);
} else if (preg_match('/^alts setmain ([a-z0-9-]+)$/i', $message, $arr)) {
	// check if new main exists
	$new_main = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($new_main);
	if (!$uid) {
		$msg = "Character <highlight>{$new_main}<end> does not exist.";
		$sendto->reply($msg);
		return;
	}
	
	$altInfo = Alts::get_alt_info($sender);
	
	if (!array_key_exists($new_main, $altInfo->alts)) {
		$msg = "<highlight>{$new_main}<end> must first be registered as your alt.";
		$sendto->reply($msg);
		return;
	}
	
	if (!$altInfo->is_validated($sender)) {
		$msg = "You must run this command from a validated character.";
		$sendto->reply($msg);
		return;
	}

	$db->begin_transaction();

	// remove all the old alt information
	$db->exec("DELETE FROM `alts` WHERE `main` = '{$altInfo->main}'");

	// add current main to new main as an alt
	Alts::add_alt($new_main, $altInfo->main, 1);
	
	// add current alts to new main
	forEach ($altInfo->alts as $alt => $validated) {
		if ($alt != $new_main) {
			Alts::add_alt($new_main, $alt, $validated);
		}
	}
	
	$db->commit();

	$msg = "Successfully set your new main as <highlight>{$new_main}<end>.";
	$sendto->reply($msg);
} else if (preg_match("/^alts ([a-z0-9-]+)$/i", $message, $arr) || preg_match("/^alts$/i", $message)) {
	if (isset($arr[1])) {
		$showValidateLinks = false;
		$name = ucfirst(strtolower($arr[1]));
	} else {
		$showValidateLinks = true;
		$name = $sender;
	}

	$altInfo = Alts::get_alt_info($name);
	if (count($altInfo->alts) == 0) {
		$msg = "No alts are registered for <highlight>{$name}<end>.";
	} else {
		$msg = $altInfo->get_alts_blob($showValidateLinks);
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>

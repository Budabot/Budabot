<?php

if (preg_match("/^altvalidate ([a-z0-9- ]+)$/i", $message, $arr)) {
	$altInfo = Alts::get_alt_info($sender);
	$alt = ucfirst(strtolower($arr[1]));
	
	$canValidate = false;
	if (Setting::get("validate_from_validated_alt") == 1) {
		// Validate from Main or Alt
		if ($altInfo->main == $sender || $altInfo->currentValidated > 0) {
			$canValidate = true;
		}
	} else {
		// Main only
		if ($altInfo->main == $sender) {
			$canValidate = true;
		}
	}
	
	// Make sure the toon is an alt of the person sending the command
	$isAlt = false;
	foreach ($altInfo->alts as $a) {
		if ($a == $alt) {
			$isAlt = true;
		}
	}
	
	
	// Alright, time to handle it
	if (!$isAlt) {
		$chatBot->send("That's not your alt!", $sendto);
	} else if ($canValidate) {
		$db->exec("UPDATE `alts` SET `validated`='1' WHERE `alt` LIKE '$alt' AND `main` LIKE '{$altInfo->main}'");
		$chatBot->send("Your alt $alt has been validated.", $sendto);
	} else {
		$chatBot->send("You're not on a character that can validate that alt.", $sendto);
	}
}

?>
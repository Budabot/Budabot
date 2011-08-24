<?php

if (Setting::get('alts_inherit_admin') == 0) {
	$chatBot->send("Alts inheriting their main's access is currently disabled.", $sendto);
} else if (preg_match("/^altvalidate ([a-z0-9- ]+)$/i", $message, $arr)) {
	$altInfo = Alts::get_alt_info($sender);
	$alt = ucfirst(strtolower($arr[1]));
	
	$canValidate = false;
	if (Setting::get("validate_from_validated_alt") == 1) {
		// Validate from Main or Alt
		if ($altInfo->main == $sender || $altInfo->is_validated($sender)) {
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
	foreach ($altInfo->alts as $a => $validated) {
		if ($a == $alt) {
			$isAlt = true;
			$isValidated = ($validated == 1);
		}
	}
	
	
	// Alright, time to handle it
	if (!$isAlt) {
		$chatBot->send("That's not your alt!", $sendto);
	} else if ($isValidated) {
		$chatBot->send("That alt is already validated!", $sendto);
	} else if ($canValidate) {
		$db->exec("UPDATE `alts` SET `validated` = '1' WHERE `alt` LIKE '$alt' AND `main` LIKE '{$altInfo->main}'");
		$chatBot->send("Your alt $alt has been validated.", $sendto);
	} else {
		$chatBot->send("You're not on a character that can validate that alt.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
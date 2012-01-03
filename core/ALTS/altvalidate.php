<?php

if (preg_match("/^altvalidate ([a-z0-9- ]+)$/i", $message, $arr)) {
	$altInfo = Alts::get_alt_info($sender);
	$alt = ucfirst(strtolower($arr[1]));

	if (!$altInfo->is_validated($sender) || ($sender != $altInfo->main && $setting->get('validate_from_validated_alt') == 0)) {
		$chatBot->send("<highlight>$alt<end> cannot be validated from your current character.", $sendto);
		return;
	}

	// Make sure the character being validated is an alt of the person sending the command
	$isAlt = false;
	forEach ($altInfo->alts as $a => $validated) {
		if ($a == $alt) {
			$isAlt = true;

			if ($validated == 1) {
				$chatBot->send("<highlight>$alt<end> is already validated as your alt.", $sendto);
				return;
			}
		}
	}

	if (!$isAlt) {
		$chatBot->send("<highlight>$alt<end> is not registered as your alt.", $sendto);
	} else {
		$db->exec("UPDATE `alts` SET `validated` = ? WHERE `alt` LIKE ? AND `main` LIKE ?", '1', $alt, $altInfo->main);
		$chatBot->send("<highlight>$alt<end> has been validated as your alt.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
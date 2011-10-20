<?php

if (preg_match("/^altsadmin add (.+) (.+)$/i", $message, $names)) {
	if ($names[1] == '' || $names[2] == '') {
		$syntax_error = true;
		return;
	}

	$name_main = ucfirst(strtolower($names[1]));
	$name_alt = ucfirst(strtolower($names[2]));
	$uid_main = $chatBot->get_uid($name_main);
	$uid_alt = $chatBot->get_uid($name_alt);

	if (!$uid_alt) {
		$msg = "The character <highlight>$name_alt<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	if (!$uid_main) {
		$msg = "The character <highlight>$name_main<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$mainInfo = Alts::get_alt_info($name_main);
	$altinfo = Alts::get_alt_info($name_alt);
	if ($altinfo->main == $mainInfo->main) {
		$msg = "The character <highlight>$name_alt<end> is already registered as an alt of <highlight>{$altinfo->main}<end>.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	if (count($altInfo->alts) > 0) {
		// already registered to someone else
		if ($altInfo->main == $name) {
			$msg = "<highlight>$name<end> is already registered as a main with alts.";
		} else {
			$msg = "<highlight>$name<end> is already registered as an of alt of {$altInfo->main}.";
		}
		$chatBot->send($msg, $sendto);
		return;
	}

	Alts::add_alt($mainInfo->main, $name_alt, 1);
	$msg = "<highlight>$name_alt<end> has been registered as an alt of {$mainInfo->main}.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^altsadmin rem (.+) (.+)$/i", $message, $names)) {
	if ($names[1] == '' || $names[2] == '') {
		$syntax_error = true;
		return;
	}

	$name_main = ucfirst(strtolower($names[1]));
	$name_alt = ucfirst(strtolower($names[2]));

	if (Alts::rem_alt($name_main, $name_alt) == 0) {
		$msg = "The character <highlight>$name_alt<end> is not listed as an alt of <highlight>$name_main<end>.";
	} else {
		$msg = "<highlight>$name_alt<end> has been removed from the alt list of <highlight>$name_main<end>.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
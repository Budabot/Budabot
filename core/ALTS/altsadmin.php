<?php

if (preg_match("/^altsadmin add ([a-z0-9-]+) ([a-z0-9-]+)$/i", $message, $names)) {
	$name_main = ucfirst(strtolower($names[1]));
	$name_alt = ucfirst(strtolower($names[2]));
	$uid_main = $chatBot->get_uid($name_main);
	$uid_alt = $chatBot->get_uid($name_alt);

	if (!$uid_alt) {
		$msg = "Character <highlight>$name_alt<end> does not exist.";
		$sendto->reply($msg);
		return;
	}
	if (!$uid_main) {
		$msg = "Character <highlight>$name_main<end> does not exist.";
		$sendto->reply($msg);
		return;
	}

	$mainInfo = Alts::get_alt_info($name_main);
	$altinfo = Alts::get_alt_info($name_alt);
	if ($altinfo->main == $mainInfo->main) {
		$msg = "Character <highlight>$name_alt<end> is already registered as an alt of <highlight>{$altinfo->main}<end>.";
		$sendto->reply($msg);
		return;
	}

	if (count($altInfo->alts) > 0) {
		// already registered to someone else
		if ($altInfo->main == $name) {
			$msg = "<highlight>$name<end> is already registered as a main with alts.";
		} else {
			$msg = "<highlight>$name<end> is already registered as an of alt of {$altInfo->main}.";
		}
		$sendto->reply($msg);
		return;
	}

	Alts::add_alt($mainInfo->main, $name_alt, 1);
	$msg = "<highlight>$name_alt<end> has been registered as an alt of {$mainInfo->main}.";
	$sendto->reply($msg);
} else if (preg_match("/^altsadmin rem ([a-z0-9-]+) ([a-z0-9-]+)$/i", $message, $names)) {
	$name_main = ucfirst(strtolower($names[1]));
	$name_alt = ucfirst(strtolower($names[2]));

	if (Alts::rem_alt($name_main, $name_alt) == 0) {
		$msg = "Character <highlight>$name_alt<end> is not listed as an alt of <highlight>$name_main<end>.";
	} else {
		$msg = "<highlight>$name_alt<end> has been removed from the alt list of <highlight>$name_main<end>.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>

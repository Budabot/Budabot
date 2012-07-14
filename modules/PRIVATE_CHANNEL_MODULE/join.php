<?php

$accessLevel = Registry::getInstance('accessLevel');

if (preg_match("/^join$/i", $message)) {
	// if the channel is locked, only raidleaders or higher can join manually
	if ($setting->get("priv_status") == "0" && !$accessLevel->checkAccess($sender, 'raidleader')) {
		if ($setting->get("priv_status_reason") != "none") {
			$sendto->reply("The private channel is locked. Reason: " . $setting->get("priv_status_reason"));
		} else {
			$sendto->reply("The private channel is locked.");
		}
		return;
	}

	$chatBot->privategroup_kick($sender);
	$chatBot->privategroup_invite($sender);
} else {
	$syntax_error = true;
}

?>

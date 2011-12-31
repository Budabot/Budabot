<?php

$accessLevel = Registry::getInstance('accessLevel');
   
if (preg_match("/^join$/i", $message)) {
	// if the channel is locked, only raidleaders or higher can join manually
	if (Setting::get("priv_status") == "0" && !$accessLevel->checkAccess($sender, 'raidleader')) {
		if (Setting::get("priv_status_reason") != "none") {
			$chatBot->send("The private channel is locked. Reason: " . Setting::get("priv_status_reason"), $sendto);
		} else {
			$chatBot->send("The private channel is locked.", $sendto);
		}
		return;
	}
	
	$chatBot->privategroup_kick($sender);
	$chatBot->privategroup_invite($sender);
} else {
	$syntax_error = true;
}

?>
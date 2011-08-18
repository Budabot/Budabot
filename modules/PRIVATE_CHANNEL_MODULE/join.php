<?php
   
if (preg_match("/^join$/i", $message)) {
	// if the channel is locked, only raidleaders or higher can join manually
	if (Setting::get("priv_status") == "0" && !AccessLevel::check_access($sender, 'raidleader')) {
		if (Setting::get("priv_status_reason") != "none") {
			$chatBot->send("The private channel is locked. Reason: " . Setting::get("priv_status_reason"), $sendto);
		} else {
			$chatBot->send("The private channel is locked.", $sendto);
		}
		return;
	}

	// if user is an admin, member, or org member, or if manual join mode is open for everyone, then invite them
	if (Setting::get("guest_man_join") == 0 || AccessLevel::check_access($sender, 'member')) {
		$chatBot->privategroup_kick($sender);
		$chatBot->privategroup_invite($sender);
	} else {
		$chatBot->send("You are not allowed to join the private channel, ask a member of the bot for an invite.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
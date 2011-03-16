<?php
   
if (preg_match("/^join$/i", $message)) {
	// if user is an admin, member, or org member, or if manual join mode is open for everyone, then invite them
	if (Setting::get("guest_man_join") == 0 || AccessLevel::checkAccess($sender, 'member')) {
		$chatBot->privategroup_kick($sender);
		$chatBot->privategroup_invite($sender);
	} else {
		$chatBot->send("You are not allowed to join the private channel, ask a member of the bot for an invite.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
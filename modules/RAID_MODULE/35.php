<?php

if (preg_match("/^35$/i", $message)) {

	// adding apf stuff
	if (Raid::add_raid_to_loot_list('APF', 'Sector 35')) {
		$msg = "Sector 35 loot table was added to the loot list.";
		$chatBot->send($msg, 'priv');
	} else {
		$msg = "Error adding Sector 35 loot table.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$msg = Raid::get_current_loot_list();
	$chatBot->send($msg, 'priv');
} else {
	$syntax_error = true;
}

?>
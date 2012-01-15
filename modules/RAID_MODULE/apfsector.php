<?php

if (preg_match("/^(13|28|35)$/i", $message, $arr)) {

	$sector = $arr[1];

	// adding apf stuff
	if (Raid::add_raid_to_loot_list('APF', "Sector $sector")) {
		$msg = "Sector $sector loot table was added to the loot list.";
		$chatBot->send($msg, 'priv');
	}

	$msg = Raid::get_current_loot_list();
	$chatBot->send($msg, 'priv');
} else {
	$syntax_error = true;
}

?>
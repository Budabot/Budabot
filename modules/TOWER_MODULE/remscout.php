<?php

$towers = Registry::getInstance('towers');
$playfields = Registry::getInstance('playfields');

if (preg_match("/^remscout ([a-z0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$playfield_name = $arr[1];
	$site_number = $arr[2];
	
	$playfield = $playfields->get_playfield_by_name($playfield_name);
	if ($playfield === null) {
		$msg = "Invalid playfield.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$tower_info = $towers->get_tower_info($playfield->id, $site_number);
	if ($tower_info === null) {
		$msg = "Invalid site number.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$num_rows = $towers->rem_scout_site($playfield->id, $site_number);
	
	if ($num_rows == 0) {
		$msg = "Could not find a scout record for {$playfield->short_name} {$site_number}.";
	} else {
		$msg = "{$playfield->short_name} {$site_number} removed successfully.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

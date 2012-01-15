<?php

$towers = Registry::getInstance('towers');
$playfields = Registry::getInstance('playfields');

if (preg_match("/^(scout|forcescout) ([a-z0-9]+) ([0-9]+) ([0-9]{1,2}:[0-9]{2}:[0-9]{2}) ([0-9]+) ([a-z]+) (.*)$/i", $message, $arr)) {
	if (strtolower($arr[1]) == 'forcescout') {
		$skip_checks = true;
	} else {
		$skip_checks = false;
	}

	$playfield_name = $arr[2];
	$site_number = $arr[3];
	$closing_time = $arr[4];
	$ct_ql = $arr[5];
	$faction = ucfirst(strtolower($arr[6]));
	$guild_name = $arr[7];
	
	if ($faction != 'Omni' && $faction != 'Neutral' && $faction != 'Clan') {
		$msg = "Valid values for faction are: 'Omni', 'Neutral', and 'Clan'.";
		$chatBot->send($msg, $sendto);
		return;
	}

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
	
	if ($ct_ql < $tower_info->min_ql || $ct_ql > $tower_info->max_ql) {
		$msg = "$playfield->short_name $tower_info->site_number can only accept Control Tower of ql {$tower_info->min_ql}-{$tower_info->max_ql}";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$closing_time_array = explode(':', $closing_time);
	$closing_time_seconds = $closing_time_array[0] * 3600 + $closing_time_array[1] * 60 + $closing_time_array[2];
	
	if (!$skip_checks && $setting->get('check_close_time_on_scout') == 1) {
		$last_victory = $towers->get_last_victory($tower_info->playfield_id, $tower_info->site_number);
		if ($last_victory !== null) {
			$victory_time_of_day = $last_attack->time % 86400;
			if ($victory_time_of_day > $closing_time_seconds) {
				$victory_time_of_day -= 86400;
			}
			
			if ($closing_time_seconds - $victory_time_of_day > 3600) {
				$check_blob .= "- <green>Closing time<end> The closing time you have specified is more than 1 hour after the site was destroyed.";
				$check_blob .= " Please verify that you are using the closing time and not the gas change time and that the closing time is correct.\n\n";
			}
		}
	}
	
	if (!$skip_checks && $setting->get('check_guild_name_on_scout') == 1) {
		if (!$towers->check_guild_name($guild_name)) {
			$check_blob .= "- <green>Org name<end> The org name you entered has never attacked or been attacked.\n\n";
		}
	}
	
	if ($check_blob) {
		$check_blob .= "Please correct these errors, or, if you are sure the values you entered are correct, use !forcescout to bypass these checks";
		$msg = Text::make_blob('Scouting problems', $check_blob);
	} else {
		$towers->add_scout_site($playfield->id, $site_number, $closing_time_seconds, $ct_ql, $faction, $guild_name, $sender);
		$msg = "Scout info has been updated.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

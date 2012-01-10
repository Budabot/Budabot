<?php

if (preg_match("/^The (Clan|Neutral|Omni) organization (.+) attacked the (Clan|Neutral|Omni) (.+) at their base in (.+). The attackers won!!$/i", $message, $arr)) {
	$win_faction = $arr[1];
	$win_guild_name = $arr[2];
	$lose_faction = $arr[3];
	$lose_guild_name = $arr[4];
	$playfield_name = $arr[5];
} else if (preg_match("/^Notum Wars Update: The (Clan|Neutral|Omni) organization (.+) lost their base in (.+).$/i", $message, $arr)) {
	$win_faction = '';
	$win_guild_name = '';
	$lose_faction = $arr[1];
	$lose_guild_name = $arr[2];
	$playfield_name = $arr[3];
} else {
	return;
}

$towers = Registry::getInstance('towers');
$playfields = Registry::getInstance('playfields');
	
$playfield = $playfields->get_playfield_by_name($playfield_name);
if ($playfield === null) {
	LegacyLogger::log('error', 'Towers', "Could not find playfield for name '$playfield_name'");
	return;
}

$last_attack = $towers->get_last_attack($win_faction, $win_guild_name, $lose_faction, $lose_guild_name, $playfield->id);
if ($last_attack !== null) {
	$towers->rem_scout_site($last_attack->playfield_id, $last_attack->site_number);
} else {
	$last_attack = new stdClass;
	$last_attack->att_guild_name = $win_guild_name;
	$last_attack->def_guild_name = $lose_guild_name;
	$last_attack->att_faction = $win_faction;
	$last_attack->def_faction = $lose_faction;
	$last_attack->playfield_id = $playfield->id;
	$last_attack->id = 'NULL';
}

$towers->record_victory($last_attack);

?>

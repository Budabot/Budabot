<?php

if (preg_match("/^The (Clan|Neutral|Omni) organization (.+) attacked the (Clan|Neutral|Omni) (.+) at their base in (.+). The attackers won!!$/i", $message, $arr)) {
	$win_faction = $arr[1];
	$win_org_name = $arr[2];
	$lose_faction = $arr[3];
	$lose_org_name = $arr[4];
	$playfield_name = $arr[5];
} else if (preg_match("/^Notum Wars Update: The (Clan|Neutral|Omni) organization (.+) lost their base in (.+).$/i", $message, $arr)) {
	$win_faction = '';
	$win_org_name = '';
	$lose_faction = $arr[3];
	$lose_org_name = $arr[4];
	$playfield_name = $arr[5];
} else {
	return;
}
	
$playfield = Playfields::get_playfield_by_name($playfield_name);
$last_attack = Towers::get_last_attack($win_faction, $win_org_name, $lose_faction, $lose_org_name, $playfield->id);
if ($last_attack !== null) {
	$sql = "DELETE FROM scout_info WHERE `playfield_id` = {$last_attack->playfield_id} AND `site_number` = {$last_attack->site_number} LIMIT 1";
	$db->exec($sql);
}

Towers::record_victory($last_attack);

?>

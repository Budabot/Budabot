<?php

$colorlabel = "<font color=#00DE42>";
$colorvalue = "<font color=#63AD63>";

if (preg_match("/^The (Clan|Neutral|Omni) organization (.+) just entered a state of war! (.+) attacked the (Clan|Neutral|Omni) organization (.+)'s tower in (.+) at location \\((\\d+),(\\d+)\\)\\.$/i", $message, $arr)) {
	$att_side = $arr[1];
	$att_guild = $arr[2];
	$att_player = $arr[3];
	$def_side = $arr[4];
	$def_guild = $arr[5];
	$playfield_name = $arr[6];
	$x_coords = $arr[7];
	$y_coords = $arr[8];
} else if (preg_match("/^(.+) just attacked the (Clan|Neutral|Omni) organization (.+)'s tower in (.+) at location \(([0-9]+), ([0-9]+)\).(.*)$/i", $message, $arr)) {
	$att_player = $arr[1];
	$def_side = $arr[2];
	$def_guild = $arr[3];
	$playfield_name = $arr[4];
	$x_coords = $arr[5];
	$y_coords = $arr[6];
} else {
	return;
}

$whois = Player::get_by_name($att_player);
if ($whois === null) {
	$whois = new stdClass;
}
if ($att_side) {
	$whois->faction = $att_side;
}
if ($att_guild) {
	$whois->guild = $att_guild;
}
// in case it's not a player who causes attack message (pet, mob, etc)
if ($att_player) {
	$whois->name = $att_player;
}

$playfield = Playfields::get_playfield_by_name($playfield_name);
$closest_site = Towers::get_closest_site($playfield->id, $x_coords, $y_coords);
if ($closest_site === null) {
	Logger::log('error', "TowerInfo", "ERROR! Could not find closest site: ({$playfield_name}) '{$playfield->id}' '{$x_coords}' '{$y_coords}'");
	$more = "[<red>UNKNOWN AREA!<end>]";
} else {

	Towers::record_attack($whois, $def_side, $def_guild, $x_coords, $y_coords, $closest_site);
	Logger::log('debug', "TowerInfo", "Site being attacked: ({$playfield_name}) '{$closest_site->playfield_id}' '{$closest_site->site_number}'");

	// Beginning of the 'more' window
	$link  = "<header>:::::: Advanced Tower Info :::::<end>\n\n";
	
	$link .= "<highlight>Attacker:<end> <font color=#DEDE42>";
	if ($whois->firstname) {
		$link .= $whois->firstname." ";
	}

	$link .= "&quot;{$att_player}&quot; ";
	if ($whois->lastname)  {
		$link .= $whois->lastname." ";
	}
	$link .= "<end>\n";
	
	if ($whois->breed) {
		$link .= $colorlabel."Breed:<end> ".$colorvalue.$whois->breed."<end>\n";
	}
	if ($whois->gender) {
		$link .= $colorlabel."Gender:<end> ".$colorvalue.$whois->gender."<end>\n";
	}

	if ($whois->profession) {
		$link .= $colorlabel."Profession:<end> ".$colorvalue.$whois->profession."<end>\n";
	}
	if ($whois->level) {
		$link .= $colorlabel."Level:<end> $colorvalue";
		if ($whois->profession == "") {
			$link .= "Unknown<end>\n";
		} else {
			$level_info = Level::get_level_info($whois->level);
			$link .= $whois->level."<end> <red>({$level_info->pvpMin}-{$level_info->pvpMax})<end>\n";
		}
	}
	
	if ($whois->ai_level) {
		$link .= $colorlabel."AI Level:<end> ".$colorvalue.$whois->ai_level;
		if ($whois->ai_rank) {
			$link .= " - ".$whois->ai_rank;
		}
		$link .= "<end>\n";
	}
		
	$link .= $colorlabel."Alignment:<end> ".$colorvalue.$whois->faction."<end>\n";
	
	if ($whois->guild) {
		if ($whois->faction == "Omni") {
			$link .= $colorlabel."Detachment:<end> ".$colorvalue.$whois->guild."<end>\n";
		} else {
			$link .= $colorlabel."Clan:<end> ".$colorvalue.$whois->guild."<end>\n";
		}
		if ($whois->guild_rank) {
			$link .= $colorlabel."Organization Rank:<end> <white>".$whois->guild_rank."<end>\n";
		}
	}


	$link .= "\n";

	$link .= "<highlight>Defender:<end> ".$colorvalue.$def_guild."<end>\n";
	$link .= $colorlabel."Alignment:<end> ".$colorvalue.$def_side."<end>\n\n";

	$base_link = $this->makeLink("{$playfield->short_name} {$closest_site->site_number}", "/tell <myname> lc {$playfield->short_name} {$closest_site->site_number}", 'chatcmd');
	$attack_waypoint = $this->makeLink("{$x_coords}x{$y_coords}", "/waypoint {$x_coords} {$y_coords} {$playfield->id}", 'chatcmd');
	$link .= "<highlight>Playfield:<end> {$colorvalue}{$base_link} ({$closest_site->min_ql}-{$closest_site->max_ql})<end>\n";
	$link .= $colorlabel."Location:<end> {$colorvalue}{$closest_site->site_name} ({$attack_waypoint})<end>\n";

	$more = "[".bot::makeLink("more", $link)."]";
}

$targetorg = "<".strtolower($def_side).">".$def_guild."<end>";

// Starting tower message to org/private chat
$msg .= "<font color=#FF67FF>[";



// tower_attack_spam >= 2 (normal) includes attacker stats
if ($this->settings["tower_attack_spam"] >= 2) {

	if ($whois->profession == "") {
		$msg .= "<".strtolower($whois->faction).">$att_player<end> (Unknown";
	} else {
		if (!$whois->guild){
			$msg .= "<".strtolower($whois->faction).">$att_player<end>";
		} else {
			$msg .= "<font color=#AAAAAA>$att_player<end>";
		}
		$msg .= " (level <font color=#AAAAAA>$whois->level<end>";
		if ($whois->ai_level) {
			$msg .= "/<green>$whois->ai_level<end>";
		}
		$msg .= ", $whois->breed <font color=#AAAAAA>$whois->profession<end>";
	}

	if (!$whois->guild) {
		$msg .= ")";
	} else if (!$whois->guild_rank) {
		$msg .= "<".strtolower($whois->faction).">$whois->guild<end>)";
	} else {
		$msg .= ", $whois->guild_rank of <".strtolower($whois->faction).">$whois->guild<end>)";
	}
	
} else if ($whois->guild) {
	$msg .= "<".strtolower($whois->faction).">$whois->guild<end>";
} else {
	$msg .= "<".strtolower($whois->faction).">$att_player<end>";
}

$msg .= " attacked ".$targetorg."] ";

// tower_attack_spam >= 3 (full) includes location.
if ($this->settings["tower_attack_spam"] >= 3) {
	if ($closest_site) {
		$site_number = "<font color=#AAAAAA>#".$closest_site->site_number."<end>";
	}
	$msg .= "[".$playfield->short_name." $site_number (".$x_coords." x ".$y_coords.")] ";
}

$msg .= "$more<end>";

$d = $this->settings["tower_faction_def"];
$a = $this->settings["tower_faction_atk"];
$s = $this->settings["tower_attack_spam"];

if (($s > 0 && (
	(strtolower($def_side) == "clan"    && ($d & 1)) ||
	(strtolower($def_side) == "neutral" && ($d & 2)) ||
	(strtolower($def_side) == "omni"    && ($d & 4)) ||
	(strtolower($whois->faction) == "clan"    && ($a & 1)) ||
	(strtolower($whois->faction) == "neutral" && ($a & 2)) ||
	(strtolower($whois->faction) == "omni"    && ($a & 4)) ))) {

	bot::send($msg, "priv", true);
}

?>

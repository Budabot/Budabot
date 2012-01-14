<?php

$colorlabel = "<font color=#00DE42>";
$colorvalue = "<font color=#63AD63>";

$page_size = $setting->get('tower_page_size');
$page_label = 1;
$search = '';

$towers = Registry::getInstance('towers');

if (preg_match("/^attacks (\\d+)$/i", $message, $arr) || preg_match("/^attacks$/i", $message, $arr)) {
	if (isset($arr[1])) {
		$page_label = $arr[1];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$chatBot->send($msg, $sendto);
			return;
		}
	}
	$cmd = "";
} else if (preg_match("/^attacks ([a-z0-9]+) (\\d+) (\\d+)$/i", $message, $arr) || preg_match("/^attacks ([a-z0-9]+) (\\d+)$/i", $message, $arr)) {
	if (isset($arr[3])) {
		$page_label = $arr[3];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$chatBot->send($msg, $sendto);
			return;
		}
	}
	
	$playfields = Registry::getInstance('playfields');
	$playfield = $playfields->get_playfield_by_name($arr[1]);
	if ($playfield === null) {
		$msg = "Please enter a valid playfield.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$tower_info = $towers->get_tower_info($playfield->id, $arr[2]);
	if ($tower_info === null) {
		$msg = "Invalid site number.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$cmd = "$arr[1] $arr[2] ";
	$search = "WHERE a.`playfield_id` = {$tower_info->playfield_id} AND a.`site_number` = {$tower_info->site_number}";
} else if (preg_match("/^attacks org (.+) (\\d+)$/i", $message, $arr) || preg_match("/^attacks org (.+)$/i", $message, $arr)) {
	if (isset($arr[2])) {
		$page_label = $arr[2];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$chatBot->send($msg, $sendto);
			return;
		}
	}

	$cmd = "org $arr[1] ";
	$value = str_replace("'", "''", $arr[1]);
	$search = "WHERE a.`att_guild_name` LIKE '$value' OR a.`def_guild_name` LIKE '$value'";
} else if (preg_match("/^attacks player (.+) (\\d+)$/i", $message, $arr) || preg_match("/^attacks player (.+)$/i", $message, $arr)) {
	if (isset($arr[2])) {
		$page_label = $arr[2];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$chatBot->send($msg, $sendto);
			return;
		}
	}

	$cmd = "player $arr[1] ";
	$value = str_replace("'", "''", $arr[1]);
	$search = "WHERE a.`att_player` LIKE '$value'";
} else {
	$syntax_error = true;
	return;
}

$start_row = ($page_label - 1) * $page_size;

$sql = 
	"SELECT
		*
	FROM
		tower_attack_<myname> a
		LEFT JOIN playfields p ON (a.playfield_id = p.id)
		LEFT JOIN tower_site s ON (a.playfield_id = s.playfield_id AND a.site_number = s.site_number)
	$search
	ORDER BY
		a.`time` DESC
	LIMIT
		$start_row, $page_size";

$data = $db->query($sql);
if (count($data) == 0) {
	$msg = "No tower attacks found.";
} else {
	$links = array();
	if ($page_label > 1) {
		$links['Previous Page'] = '/tell <myname> attacks ' . ($page_label - 1);
	}
	$links['Next Page'] = "/tell <myname> attacks {$cmd}" . ($page_label + 1);
	
	$blob = Text::make_header("The last $page_size Tower Attacks (page $page_label)", $links);
	$blob .= $colorvalue;

	forEach ($data as $row) {
		$blob .= $colorlabel."Time:<end> ".date(Util::DATETIME, $row->time)." (GMT)\n";
		if ($row->att_faction == '') {
			$att_faction = "unknown";
		} else {
			$att_faction = strtolower($row->att_faction);
		}

		if ($row->def_faction == '') {
			$def_faction = "unknown";
		} else {
			$def_faction = strtolower($row->def_faction);
		}

		if ($row->att_profession == 'Unknown') {
			$blob .= $colorlabel."Attacker:<end> <{$att_faction}>{$row->att_player}<end> ({$row->att_faction})\n";
		} else if ($row->att_guild_name == '') {
			$blob .= $colorlabel."Attacker:<end> <{$att_faction}>{$row->att_player}<end> ({$row->att_level}/<green>{$row->att_ai_level}<end> {$row->att_profession}) ({$row->att_faction})\n";
		} else {
			$blob .= $colorlabel."Attacker:<end> {$row->att_player} ({$row->att_level}/<green>{$row->att_ai_level}<end> {$row->att_profession}) <{$att_faction}>{$row->att_guild_name}<end> ({$row->att_faction})\n";
		}
		
		$base = Text::make_chatcmd("{$row->short_name} {$row->site_number}", "/tell <myname> lc {$row->short_name} {$row->site_number}");
		$base .= " ({$row->min_ql}-{$row->max_ql})";

		$blob .= $colorlabel."Defender:<end> <{$def_faction}>{$row->def_guild_name}<end> ({$row->def_faction})\n";
		$blob .= "Site: $base\n\n";
	}
	$msg = Text::make_blob("Tower Attacks", $blob);
}

$chatBot->send($msg, $sendto);

?>

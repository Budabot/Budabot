<?php

$colorlabel = "<font color=#00DE42>";
$colorvalue = "<font color=#63AD63>";

$listcount = 20;
$page_label = 1;
$search = '';

if (preg_match("/^(attacks|battles?) (\\d+)$/i", $message, $arr) || preg_match("/^(attacks|battles?)$/i", $message, $arr)) {
	if (isset($arr[2])) {
		$page_label = $arr[2];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}
} else if (preg_match("/^(attacks|battles?) ([a-z0-9]+) (\\d+) (\\d+)$/i", $message, $arr) || preg_match("/^(attacks|battles?) ([a-z0-9]+) (\\d+)$/i", $message, $arr)) {
	if (isset($arr[4])) {
		$page_label = $arr[4];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}
	
	$playfield = Playfields::get_playfield_by_name($arr[2]);
	if ($playfield === null) {
		$msg = "Please enter a valid playfield.";
		bot::send($msg, $sendto);
		return;
	}
	
	$tower_info = Towers::get_tower_info($playfield->id, $arr[3]);
	if ($tower_info === null) {
		$msg = "Invalid site number.";
		bot::send($msg, $sendto);
		return;
	}

	$search = "WHERE a.`playfield_id` = {$tower_info->playfield_id} AND a.`site_number` = {$tower_info->site_number}";
} else if (preg_match("/^(attacks|battles?) org (.+) (\\d+)$/i", $message, $arr) || preg_match("/^(attacks|battles?) org (.+)$/i", $message, $arr)) {
	if (isset($arr[3])) {
		$page_label = $arr[3];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}

	$value = str_replace("'", "''", $arr[2]);
	$search = "WHERE a.`att_org_name` LIKE '$value' OR a.`def_org_name` LIKE '$value'";
} else if (preg_match("/^(attacks|battles?) player (.+) (\\d+)$/i", $message, $arr) || preg_match("/^(attacks|battles?) player (.+)$/i", $message, $arr)) {
	if (isset($arr[3])) {
		$page_label = $arr[3];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}
	
	$value = str_replace("'", "''", $arr[2]);
	$search = "WHERE a.`att_player` LIKE '$value'";
} else {
	$syntax_error = true;
	return;
}

$page = $page_label - 1;
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
		$page, $listcount";

$db->query($sql);

if ($db->numrows() == 0) {
	$msg = "No tower attacks found.";
} else {
	$list = "<header>::::: The last $listcount Tower Attacks (page $page_label) :::::<end>\n\n" . $colorvalue;

	while ($row = $db->fObject()) {
		$list .= $colorlabel."Time:<end> ".gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
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
			$list .= $colorlabel."Attacker:<end> <{$att_faction}>{$row->att_player}<end> ({$row->att_faction})\n";
		} else if ($row->att_org_name == '') {
			$list .= $colorlabel."Attacker:<end> <{$att_faction}>{$row->att_player}<end> (Lvl {$row->att_level}/{$row->att_profession}) ({$row->att_faction})\n";
		} else {
			$list .= $colorlabel."Attacker:<end> {$row->att_player} (Lvl {$row->att_level}/{$row->att_profession}/<{$att_faction}>{$row->att_org_name}<end>) ({$row->att_faction})\n";
		}
		
		$base = $this->makeLink("{$row->short_name} {$row->site_number}", "/tell <myname> lc {$row->short_name} {$row->site_number}", 'chatcmd');
		$base .= " ({$row->min_ql}-{$row->max_ql})";

		$list .= $colorlabel."Defender:<end> <{$def_faction}>{$row->def_org_name}<end> ({$row->def_faction})\n";
		$list .= "Site: $base\n\n";
	}
	$msg = bot::makeLink("Tower Attacks", $list);
}

bot::send($msg, $sendto);

?>

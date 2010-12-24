<?php

$colorlabel = "<font color=#00DE42>";
$colorvalue = "<font color=#63AD63>";

$listcount = 20;
$page_label = 1;
$search = '';

if (preg_match("/^victory (\\d+)$/i", $message, $arr) || preg_match("/^victory$/i", $message, $arr)) {
	if (isset($arr[1])) {
		$page_label = $arr[1];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}
} else if (preg_match("/^victory ([a-z0-9]+) (\\d+) (\\d+)$/i", $message, $arr) || preg_match("/^victory ([a-z0-9]+) (\\d+)$/i", $message, $arr)) {
	if (isset($arr[3])) {
		$page_label = $arr[3];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}
	
	$playfield = Playfields::get_playfield_by_name($arr[1]);
	if ($playfield === null) {
		$msg = "Invalid playfield.";
		bot::send($msg, $sendto);
		return;
	}
	
	$tower_info = Towers::get_tower_info($playfield->id, $arr[2]);
	if ($tower_info === null) {
		$msg = "Invalid site number.";
		bot::send($msg, $sendto);
		return;
	}

	$search = "WHERE `a.playfield_id` = {$tower_info->playfield_id} AND `a.site_number` = {$tower_info->site_number}";
} else if (preg_match("/^victory org (.+) (\\d+)$/i", $message) || preg_match("/^victory org (.+)$/i", $message)) {
	if (isset($arr[2])) {
		$page_label = $arr[2];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}

	$value = str_replace("'", "''", $arr[1]);
	$search = "WHERE v.`win_guild_name` LIKE '$value' OR v.`lose_guild_name` LIKE '$value'";
} else if (preg_match("/^victory player (.+) (\\d+)$/i", $message) || preg_match("/^victory player (.+)$/i", $message)) {
	if (isset($arr[2])) {
		$page_label = $arr[2];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$this->send($msg, $sendto);
			return;
		}
	}
	
	$value = str_replace("'", "''", $arr[1]);
	$search = "WHERE a.`att_player` LIKE '$value'";
} else {
	$syntax_error = true;
	return;
}

$page = $page_label - 1;
$sql = "
	SELECT
		*,
		v.time AS victory_time,
		a.time AS attack_time
	FROM
		tower_victory v
		LEFT JOIN tower_attack a ON (v.attack_id = a.id)
		LEFT JOIN playfields p ON (a.playfield_id = p.id)
		LEFT JOIN tower_site s ON (a.playfield_id = s.playfield_id AND a.site_number = s.site_number)
	{$search}
	ORDER BY
		v.`time` DESC
	LIMIT
		$page, $listcount";

$db->query($sql);
if ($db->numrows() == 0) {
	$msg = "No Tower results found.";
} else {
	$list = "<header>::::: The last $listcount Tower Results (page $page_label) :::::<end>\n\n".$colorvalue;
	while ($row = $db->fObject()) {
		$list .= $colorlabel."Time:<end> ".gmdate("M j, Y, G:i", $row->victory_time)." (GMT)\n";

		if (!$win_side = strtolower($row->win_faction)) {
			$win_side = "unknown";
		}
		if (!$lose_side = strtolower($row->lose_faction)) {
			$lose_side = "unknown";
		}
		
		if ($row->playfield_id != '' && $row->site_number != '') {
			$base = $this->makeLink("{$row->short_name} {$row->site_number}", "/tell <myname> lc {$row->short_name} {$row->site_number}", 'chatcmd');
			$base .= " ({$row->min_ql}-{$row->max_ql})";
		} else {
			$base = "Unknown";
		}

		$list .= $colorlabel."Winner:<end> <{$win_side}>{$row->win_org_name}<end> (".ucfirst($win_side).")\n";
		$list .= $colorlabel."Loser:<end> <{$lose_side}>{$row->lose_org_name}<end> (".ucfirst($lose_side).")\n";
		$list .= "Site: $base\n\n";
	}
	$msg = bot::makeLink("Tower Victories", $list);
}
 
bot::send($msg, $sendto);

?>

<?php

$colorlabel = "<font color=#00DE42>";
$colorvalue = "<font color=#63AD63>";

$page_size = $setting->get('tower_page_size');
$page_label = 1;
$search = '';

$towers = Registry::getInstance('towers');
$playfields = Registry::getInstance('playfields');

if (preg_match("/^victory (\\d+)$/i", $message, $arr) || preg_match("/^victory$/i", $message, $arr)) {
	if (isset($arr[1])) {
		$page_label = $arr[1];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$sendto->reply($msg);
			return;
		}
	}
	$cmd = "";
} else if (preg_match("/^victory ([a-z0-9]+) (\\d+) (\\d+)$/i", $message, $arr) || preg_match("/^victory ([a-z0-9]+) (\\d+)$/i", $message, $arr)) {
	if (isset($arr[3])) {
		$page_label = $arr[3];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$sendto->reply($msg);
			return;
		}
	}

	$playfield = $playfields->get_playfield_by_name($arr[1]);
	if ($playfield === null) {
		$msg = "Invalid playfield.";
		$sendto->reply($msg);
		return;
	}

	$tower_info = $towers->get_tower_info($playfield->id, $arr[2]);
	if ($tower_info === null) {
		$msg = "Invalid site number.";
		$sendto->reply($msg);
		return;
	}

	$cmd = "$arr[1] $arr[2] ";
	$search = "WHERE a.`playfield_id` = {$tower_info->playfield_id} AND a.`site_number` = {$tower_info->site_number}";
} else if (preg_match("/^victory org (.+) (\\d+)$/i", $message, $arr) || preg_match("/^victory org (.+)$/i", $message, $arr)) {
	if (isset($arr[2])) {
		$page_label = $arr[2];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$sendto->reply($msg);
			return;
		}
	}

	$cmd = "org $arr[1] ";
	$value = str_replace("'", "''", $arr[1]);
	$search = "WHERE v.`win_guild_name` LIKE '$value' OR v.`lose_guild_name` LIKE '$value'";
} else if (preg_match("/^victory player (.+) (\\d+)$/i", $message, $arr) || preg_match("/^victory player (.+)$/i", $message, $arr)) {
	if (isset($arr[2])) {
		$page_label = $arr[2];
		if ($page_label < 1) {
			$msg = "You must choose a page number greater than 0";
			$sendto->reply($msg);
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

$sql = "
	SELECT
		*,
		v.time AS victory_time,
		a.time AS attack_time
	FROM
		tower_victory_<myname> v
		LEFT JOIN tower_attack_<myname> a ON (v.attack_id = a.id)
		LEFT JOIN playfields p ON (a.playfield_id = p.id)
		LEFT JOIN tower_site s ON (a.playfield_id = s.playfield_id AND a.site_number = s.site_number)
	{$search}
	ORDER BY
		`victory_time` DESC
	LIMIT
		$start_row, $page_size";

$data = $db->query($sql);
if (count($data) == 0) {
	$msg = "No Tower results found.";
} else {
	$links = array();
	if ($page_label > 1) {
		$links['Previous Page'] = '/tell <myname> victory ' . ($page_label - 1);
	}
	$links['Next Page'] = "/tell <myname> victory {$cmd}" . ($page_label + 1);

	$blob = "The last $page_size Tower Results (page $page_label)\n\n";
	$blob .= Text::make_header_links($links) . "\n\n";
	$blob .= $colorvalue;
	forEach ($data as $row) {
		$blob .= $colorlabel."Time:<end> ".date(Util::DATETIME, $row->victory_time)." (GMT)\n";

		if (!$win_side = strtolower($row->win_faction)) {
			$win_side = "unknown";
		}
		if (!$lose_side = strtolower($row->lose_faction)) {
			$lose_side = "unknown";
		}

		if ($row->playfield_id != '' && $row->site_number != '') {
			$base = Text::make_chatcmd("{$row->short_name} {$row->site_number}", "/tell <myname> lc {$row->short_name} {$row->site_number}");
			$base .= " ({$row->min_ql}-{$row->max_ql})";
		} else {
			$base = "Unknown";
		}

		$blob .= $colorlabel."Winner:<end> <{$win_side}>{$row->win_guild_name}<end> (".ucfirst($win_side).")\n";
		$blob .= $colorlabel."Loser:<end> <{$lose_side}>{$row->lose_guild_name}<end> (".ucfirst($lose_side).")\n";
		$blob .= "Site: $base\n\n";
	}
	$msg = Text::make_blob("Tower Victories", $blob);
}

$sendto->reply($msg);

?>

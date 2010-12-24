<?php

function getTowerType($ql) {
	$towerType = '';
	
	if ($ql >= 276) {
		$towerType = "VIII";
	} else if ($ql >= 226) {
		$towerType = "VII";
	} else if ($ql >= 201) {
		$towerType = "VI";
	} else if ($ql >= 177) {
		$towerType = "V";
	} else if ($ql >= 129) {
		$towerType = "IV";
	} else if ($ql >= 82) {
		$towerType = "III";
	} else if ($ql >= 34) {
		$towerType = "II";
	} else {
		$towerType = "I";
	}
	
	return $towerType;	
}

function getOpenTimeSql($current_time) {
	$first_high_val = $current_time + (3600*7);
	$first_low_val = $current_time;
	$second_high_val = $current_time - 86400 + (3600*7);
	$second_low_val = $current_time - 86400;

	return "((s.close_time BETWEEN $first_low_val AND $first_high_val) OR (s.close_time BETWEEN $second_low_val AND $second_high_val))";
}

function getGasLevel($close_time) {
	$current_time = time() % 86400;

	$site = new stdClass();
	$site->current_time = $current_time;
	$site->close_time = $close_time;
	
	if ($close_time < $current_time) {
		$close_time += 86400;
	}

	$time_until_close_time = $close_time - $current_time;
	$site->time_until_close_time = $time_until_close_time;
	
	if ($time_until_close_time < 3600 * 1) {
		$site->gas_change = $time_until_close_time;
		$site->gas_level = '5%';
		$site->next_state = 'closes';
		$site->color = "<orange>";
	} else if ($time_until_close_time < 3600 * 6) {
		$site->gas_change = $time_until_close_time;
		$site->gas_level = '25%';
		$site->next_state = 'closes';
		$site->color = "<green>";
	} else {
		$site->gas_change = $time_until_close_time - (3600 * 6);
		$site->gas_level = '75%';
		$site->next_state = 'opens';
		$site->color = "<red>";
	}
	
	return $site;
}

function formatSiteInfo($row) {
	global $chatBot;

	$waypoint = $chatBot->makeLink($row->x_coord . "x" . $row->y_coord, "/waypoint {$row->x_coord} {$row->y_coord} {$row->playfield_id}", 'chatcmd');

	$blob =
"<font color=#66aa66>Short name:</font> <white>{$row->short_name} {$row->site_number}<end>
<font color=#66aa66>Long name:</font> <white>{$row->site_name}, {$row->long_name}<end>
<font color=#66aa66>Level range:</font> <white>{$row->min_ql}-{$row->max_ql}<end>
<font color=#66aa66>Centre coordinates:</font> <a href='chatcmd:///waypoint {$row->x_coord} {$row->y_coord} {$row->playfield_id}'>{$row->x_coord}x{$row->y_coord}</a>
<a href='chatcmd:///tell <myname> attacks {$row->short_name} {$row->site_number}'>Recent attacks on this base</a>";
	
	return $blob;
}

?>

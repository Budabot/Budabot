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
	$gas_level = getGasLevel($row->close_time);
	if ($row->topic != '') {
		$baseTopicLink = $chatBot->makeLink('Use this topic', "/tell <myname> basetopic $row->zone $row->site_number", 'chatcmd');
		$topic = "<yellow>$row->topic<end> [by $row->topic_by] [$baseTopicLink]\n";
	}

	$type = getTowerType($row->ct_ql);
	$close_time = gmdate("H:i:s T", $row->close_time);

	$rally = '';
	if (!is_null($row->x_rally) && !is_null($row->y_rally)) {
		$rally = "(Rally: <a href='chatcmd:///waypoint {$row->x_rally} {$row->y_rally} {$row->playfield_id}'>{$row->x_rally}x{$row->y_rally}</a>)";
	}

	$topic = '';
	if ($row->topic == '') {
		$topic .= "Not set";
	} else {
		$topic .= "{$row->topic} {$rally} [by {$row->topic_by}] [<a href='chatcmd:///tell <myname> basetopic {$row->short_name} {$row->site_number}'>Use this topic</a>]";
	}
	
	$out_of_date = '';
	if ($row->is_current == 0) {
		$out_of_date = "<red>(Out of date - needs rescouting)<end>";
	}

	$blob =
"<font color=#66aa66>Short name:</font> <white>{$row->short_name} {$row->site_number}<end>
<font color=#66aa66>Long name:</font> <white>{$row->site_name}, {$row->long_name}<end>
<font color=#66aa66>Level range:</font> <white>{$row->min_ql}-{$row->max_ql}<end>
<font color=#66aa66>Centre coordinates:</font> <a href='chatcmd:///waypoint {$row->x_coord} {$row->y_coord} {$row->playfield_id}'>{$row->x_coord}x{$row->y_coord}</a>
<font color=CCInfoHeader>Scouted on {$row->scouted_on} by {$row->scouted_by}:<end> {$out_of_date}
<font color=#66aa66>Current owner:</font> <white>{$row->org_name}  ({$row->faction})<end>
<font color=#66aa66>CT QL:</font> <white>{$row->ct_ql}<end>   <font color=#66aa66>Type:</font> <white>{$type}<end>   <font color=#66aa66>Close time:</font> <white>{$close_time}<end>
<a href='chatcmd:///tell <myname> attacks {$row->short_name} {$row->site_number}'>Recent attacks on this base</a>";
	
	return $blob;
}

?>

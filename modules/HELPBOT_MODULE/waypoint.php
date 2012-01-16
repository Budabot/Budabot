<?php

$playfields = Registry::getInstance('playfields');

if (preg_match("/^waypoint \\(?([0-9\\.]+) ([0-9\\.]+) y ([0-9\\.]+) ([0-9]+)\\)?$/i", $message, $arr)) {
	$x_coords = $arr[1];
	$y_coords = $arr[2];
	$playfield_id = $arr[4];

	$playfield = $playfields->get_playfield_by_id($playfield_id);
	if ($playfield === null) {
		$playfield_name = $playfield_id;
	} else {
		$playfield_name = $playfield->short_name;
	}
} else if (preg_match("/^waypoint ([0-9\\.]+)([x,. ]+)([0-9\\.]+)([x,. ]+)([0-9]+)$/i", $message, $arr)) {
	$x_coords = $arr[1];
	$y_coords = $arr[3];
	$playfield_id = $arr[5];
	
	$playfield = $playfields->get_playfield_by_id($playfield_id);
	if ($playfield === null) {
		$playfield_name = $playfield_id;
	} else {
		$playfield_name = $playfield->short_name;
	}
} else if (preg_match("/^waypoint ([0-9\\.]+)([x,. ]+)([0-9\\.]+)([x,. ]+)(.+)$/i", $message, $arr)) {
	$x_coords = $arr[1];
	$y_coords = $arr[3];
	$playfield_name = $arr[5];
	
	$playfield = $playfields->get_playfield_by_name($playfield_name);
	if ($playfield === null) {
		$sendto->reply("Could not find playfield '$playfield_name'");
		return;
	} else {
		$playfield_id = $playfield->id;
		$playfield_name = $playfield->short_name;
	}
} else {
	$syntax_error = true;
	return;
}

$link = Text::make_chatcmd("waypoint: {$x_coords}x{$y_coords} {$playfield_name}", "/waypoint {$x_coords} {$y_coords} {$playfield_id}");	
$blob = "Click here to use waypoint: $link";
$msg = Text::make_blob("waypoint: {$x_coords}x{$y_coords} {$playfield_name}", $blob);
$sendto->reply($msg);

?>
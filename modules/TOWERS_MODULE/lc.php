<?php

if (preg_match("/^lc$/i", $message, $arr)) {
	$sql = "SELECT * FROM playfields WHERE `id` IN (SELECT DISTINCT `playfield_id` FROM tower_site) ORDER BY `short_name`";
	$db->query($sql);
	
	$blob = "Land Control Index\n\n";
	while (($row = $db->fObject()) != FALSE) {
		$baseLink = $this->makeLink($row->long_name, "/tell <myname> lc $row->short_name", 'chatcmd');
		$blob .= "$baseLink <highlight>($row->short_name)<end>\n";
	}
	$msg = $this->makeLink('Land Control Index', $blob, 'blob');
	$this->send($msg, $sendto);
} else if (preg_match("/^lc ([0-9a-z]+)$/i", $message, $arr)) {
	$playfield_name = strtoupper($arr[1]);
	$playfield = Playfields::get_playfield_by_name($playfield_name);
	if ($playfield === null) {
		$msg = "Playfield '$playfield_name' could not be found";
		$this->send($msg, $sendto);
		return;
	}

	$sql = "SELECT * FROM tower_site t1
		JOIN playfields p ON (t1.playfield_id = p.id)
		WHERE t1.playfield_id = $playfield->id";

	$db->query($sql);
	$numrows = $db->numrows();
	$blob = "All bases in $playfield->long_name\n\n";
	while (($row = $db->fObject()) != FALSE) {
		$gas_level = getGasLevel($row->close_time);
		$blob .= formatSiteInfo($row) . "\n\n";
	}
	
	$msg = bot::makeLink("All Bases in $playfield->long_name", $blob, 'blob');
	
	bot::send($msg, $sendto);
} else if (preg_match("/^lc ([0-9a-z]+) ([0-9]+)$/i", $message, $arr)) {
	$playfield_name = strtoupper($arr[1]);
	$playfield = Playfields::get_playfield_by_name($playfield_name);
	if ($playfield === null) {
		$msg = "Playfield '$playfield_name' could not be found";
		$this->send($msg, $sendto);
		return;
	}

	$site_number = $arr[2];
	$sql = "SELECT * FROM tower_site t1
		JOIN playfields p ON (t1.playfield_id = p.id)
		WHERE t1.playfield_id = $playfield->id AND t1.site_number = $site_number";

	$db->query($sql);
	$numrows = $db->numrows();
	$blob = "$playfield->short_name $site_number\n\n";
	while (($row = $db->fObject()) != FALSE) {
		$gas_level = getGasLevel($row->close_time);
		$blob .= formatSiteInfo($row) . "\n\n";
	}
	
	if ($numrows > 0) {
		$msg = bot::makeLink("$playfield->short_name $site_number", $blob, 'blob');
	} else {
		$msg = "Invalid site number.";
	}
	
	bot::send($msg, $sendto);
} else if (preg_match("/^lc org (.+)$/i", $message, $arr)) {
	$org = $arr[1];
	
	$org = str_replace("'", "''", $org);
	$sql = "SELECT * FROM tower_site t1
		JOIN playfields p ON (t1.playfield_id = p.id)
		WHERE s.org_name LIKE '$org'";

	$db->query($sql);
	$numrows = $db->numrows();
	while (($row = $db->fObject()) != FALSE) {
		$gas_level = getGasLevel($row->close_time);
		$blob .= formatSiteInfo($row) . "\n\n";
	}
	
	if ($numrows > 0) {
		$msg = bot::makeLink("Bases belonging to $org", $blob, 'blob');
	} else {
		$msg = "Could not find any sites for org '$org'";
	}
	
	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
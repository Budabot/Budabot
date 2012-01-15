<?php

if (preg_match("/^orgcities$/i", $message)) {
	$data = $db->query("SELECT DISTINCT playfield_id, long_name, short_name FROM orgcities c JOIN playfields p ON c.playfield_id = p.id ORDER BY long_name ASC");
	
	$blob = '';
	forEach ($data as $row) {
		$cityLink = Text::make_chatcmd($row->long_name, "/tell <myname> orgcities $row->short_name");
		$blob .= $cityLink . "\n";
	}
	
	$msg = Text::make_blob("Playfields with Org Cities", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^orgcities (.+)$/i", $message, $arr)) {
	$playfields = Registry::getInstance('playfields');
	$playfield = $playfields->get_playfield_by_name($arr[1]);
	if ($playfield === null) {
		$chatBot->send("Could not find playfield '{$arr[1]}'", $sendto);
		return;
	}

	$data = $db->query("SELECT * FROM orgcities WHERE playfield_id = ? ORDER BY cluster ASC, plot ASC", $playfield->id);
	
	$blob = '';
	$current_cluster = '';
	forEach ($data as $row) {
		if ($current_cluster != $row->cluster) {
			$blob .= "\n<highlight>Cluster {$row->cluster}<end>\n";
			$current_cluster = $row->cluster;
		}
		$coords = Text::make_chatcmd("{$row->xcoord}x{$row->ycoord}", "/waypoint {$row->xcoord} {$row->ycoord} {$row->playfield_id}");
		$blob .= $row->cluster . $row->plot . " {$coords}\n";
	}
	
	$msg = Text::make_blob("Org cities in {$playfield->long_name}", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

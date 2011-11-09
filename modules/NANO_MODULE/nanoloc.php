<?php

if (preg_match("/^nanoloc$/i", $message, $arr)) {
	$db->query("SELECT DISTINCT location FROM nanos");
	$data = $db->fObject('all');
	
	$blob = "<header> :::::: Nano Locations :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .= Text::make_chatcmd($row->location, "/tell <myname> nanoloc $row->location") . "\n";
	}
	
	$msg = Text::make_blob("Nano Locations", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^nanoloc (.+)$/i", $message, $arr)) {
	$location = $arr[1];

	$db->query("SELECT * FROM nanos WHERE location LIKE '" . str_replace("'", "''", $location) . "' ORDER BY lowql DESC, name");
	$data = $db->fObject('all');
	$count = count($data);
	if ($count == 0) {
		$msg = "No nanos found.";
	} else if ($count == 1) {
		$row = $data[0];
		$msg .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name) . " ({$row->lowql})\n";
		$msg .= "Located: {$row->location}";
	} else {
		$blob = "<header> :::::: Nanos for Location '$location' ($count) :::::: <end>\n\n";
		forEach ($data as $row) {
			$blob .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name) . " ({$row->lowql})\n";
			$blob .= "Located: {$row->location}\n\n";
		}
		
		$msg = Text::make_blob("Nanos for Location '$location' ($count)", $blob);
	}

	$chatBot->send($msg, $sendto);
} else {
  	$syntax_error = true; 	
}

?>
<?php

if (preg_match("/^nanoloc$/i", $message, $arr)) {
	$data = $db->query("SELECT location, count(location) AS count FROM nanos GROUP BY location ORDER BY location ASC");
	
	$header = "Nano Locations";
	$blob = Text::make_header($header, array('Help' => '/tell <myname> help nano'));
	forEach ($data as $row) {
		$blob .= Text::make_chatcmd($row->location, "/tell <myname> nanoloc $row->location") . " ($row->count) \n";
	}
	
	$msg = Text::make_blob($header, $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^nanoloc (.+)$/i", $message, $arr)) {
	$location = $arr[1];

	$sql = 
		"SELECT
			n1.lowid,
			n1.lowql,
			n1.name,
			n1.location,
			n3.profession
		FROM
			nanos n1
			LEFT JOIN nano_nanolines_ref n2 ON n1.lowid = n2.lowid
			LEFT JOIN nanolines n3 ON n2.nanolineid = n3.id
		WHERE
			n1.location LIKE ?
		ORDER BY
			n1.profession ASC,
			n1.name ASC";

	$data = $db->query($sql, $location);

	$count = count($data);
	if ($count == 0) {
		$msg = "No nanos found.";
	} else {
		$header = "Nanos for Location '$location' ($count)";
		$blob = Text::make_header($header, array('Help' => '/tell <myname> help nano'));
		forEach ($data as $row) {
			$blob .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
			$blob .= " [$row->lowql] $row->location";
			if ($row->profession) {
				$blob .= " - <highlight>$row->profession<end>";
			}
			$blob .= "\n";
		}
		
		$msg = Text::make_blob($header, $blob);
	}

	$chatBot->send($msg, $sendto);
} else {
  	$syntax_error = true; 	
}

?>
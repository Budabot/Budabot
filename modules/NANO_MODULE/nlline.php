<?php

if (preg_match("/^nlline ([0-9]+)$/i", $message, $arr)) {

	$nanoline_id = $arr[1];

	$sql = "SELECT * FROM nanolines WHERE id = $nanoline_id";
	$db->query($sql);

	$msg = '';
	if ($row = $db->fObject()) {

		$header = "$row->profession $row->name Nanos";

		$window = Text::make_header($header, array('Help' => '/tell <myname> help nanolines'));

		$sql = "
			SELECT
				n1.lowid,
				lowql,
				n1.name,
				location
			FROM
				nanos n1
				JOIN nano_nanolines_ref n2
					ON (n1.lowid = n2.lowid)
			WHERE
				n2.nanolineid = $nanoline_id
			ORDER BY
				lowql DESC, name ASC";
		$db->query($sql);
		$data = $db->fObject('all');

		forEach ($data as $row) {
			$window .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
			$window .= " [$row->lowql] $row->location\n";
		}

		$window .= "\n\nAO Nanos by Voriuste";
		$window .= "\nModule created by Tyrence (RK2)";

		$msg = Text::make_blob($header, $window);

	} else {
		$msg = "No nanoline found.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

<?php
   /*
   ** The Majority of this code was written by Derroylo (RK2) for the 
   ** Budabot Items Module.  I just hacked it to use Nano DB from a
   ** Similar Bebot nano Module.
   **
   ** Healnjoo RK2
   */
   
if (preg_match("/^nano (.+)$/i", $message, $arr)) {
    $name = $arr[1];
	
	$name = htmlspecialchars_decode($name);
	$name = str_replace("'", "''", $name);

	$tmp = explode(" ", $name);
	forEach ($tmp as $key => $value) {
		$query .= " AND n1.`name` LIKE '%$value%'";
	}

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
			1=1 $query
		ORDER BY
			n1.lowql DESC, n1.name ASC
		LIMIT
			" . $setting->get("maxnano");
	
	$data = $db->query($sql);

	$count = count($data);
	if ($count == 0) {
		$msg = "No nanos found.";
	} else if ($count == 1) {
		$row = $data[0];
		$msg = Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
		$msg .= " [$row->lowql] $row->location";
		if ($row->profession) {
			$msg .= " - <highlight>$row->profession<end>";
		}
	} else {
		$blob = '';
		forEach ($data as $row) {
			$blob .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
			$blob .= " [$row->lowql] $row->location";
			if ($row->profession) {
				$blob .= " - <highlight>$row->profession<end>";
			}
			$blob .= "\n";
		}
		
		$msg = Text::make_blob("Nano Search Results ($count)", $blob);
	}

	$sendto->reply($msg);
} else {
  	$syntax_error = true; 	
}

?>
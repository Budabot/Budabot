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
	
	$name = str_replace(":", "&#58;", $name);
	$name = str_replace("&", "&amp;", $name);
	$name = str_replace("'", "''", $name);

	$tmp = explode(" ", $name);
	forEach ($tmp as $key => $value) {
		$query .= " AND `name` LIKE '%$value%'";
	}

	$db->query("SELECT * FROM nanos WHERE 1=1 $query ORDER BY lowql DESC, name LIMIT 0, " . Setting::get("maxnano"));
	$count = $db->numrows();
	if ($count == 0) {
		$msg = "No nanos found.";
	} else if ($count == 1) {
		$row = $db->fObject();
		$msg .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name) . " ({$row->lowql})\n";
		$msg .= "Located: {$row->location}";
	} else {
		$blob = "<header> :::::: Nano Search Results ($count) :::::: <end>\n\n";
		$data = $db->fObject('all');
		forEach ($data as $row) {
			$blob .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name) . " ({$row->lowql})\n";
			$blob .= "Located: {$row->location}\n\n";
		}
		
		$msg = Text::make_blob("Nano Search Results ($count)", $blob);
	}

	$chatBot->send($msg, $sendto);
} else {
  	$syntax_error = true; 	
}

?>
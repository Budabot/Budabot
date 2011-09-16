<?php

if (preg_match("/^whompah$/i", $message, $arr)) {
	$sql = "SELECT * FROM `whompah_cities` ORDER BY city_name ASC";
	$db->query($sql);
	$data = $db->fObject('all');

	$blob = "<header> :::::: Whompah Cities :::::: <end>\n\n<white>";
	forEach ($data as $row) {
		$cityLink = Text::make_chatcmd($row->short_name, "/tell <myname> whompah {$row->short_name}");
		$blob .= "{$row->city_name} ({$cityLink})\n";
	}
	$blob .= "\nWritten By Tyrence (RK2)\nDatabase from a Bebot module written by POD13";
	
	$msg = Text::make_blob('Whompah Cities', $blob);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^whompah (.+) (.+)$/i", $message, $arr)) {
	$startCity = Whompah::find_city($arr[1]);
	$endCity = Whompah::find_city($arr[2]);
	
	if ($startCity === null) {
		$msg = "Error! Could not find city '$arr[1]'!";
		$chatBot->send($msg, $sendto);
		return;
	}
	if ($endCity === null) {
		$msg = "Error! Could not find city '$arr[2]'!";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$whompahs = Whompah::build_whompah_network();
	
	$whompah = new stdClass;
	$whompah->id = $endCity->id;
	$whompah->city_name = $whompahs[$endCity->id]->city_name;
	$whompah->previous = null;
	$whompah->visited = true;
	$obj = Whompah::find_whompah_path($q = array($whompah), $whompahs, $startCity->id);
	
	if ($obj === false) {
		$msg = "There was an error while trying to find the whompah path.";
	} else {
		while ($obj->previous !== null) {
			$msg .= "$obj->city_name -> ";
			$obj = &$obj->previous;
		}
		$msg .= "$obj->city_name";
	}
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^whompah (.+)$/i", $message, $arr)) {
	$city = Whompah::find_city($arr[1]);
	
	if ($city === null) {
		$msg = "Error! Could not find city '$arr[1]'!";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$sql = "SELECT w2.* FROM whompah_cities_rel w1 JOIN whompah_cities w2 ON w1.city2_id = w2.id WHERE w1.city1_id = $city->id";
	$db->query($sql);
	$data = $db->fObject('all');
	
	$msg = "From {$city->city_name} you can get to: ";
	forEach ($data as $row) {
		$msg .= "<yellow>{$row->city_name}<end> (<highlight>{$row->short_name}<end>), ";
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

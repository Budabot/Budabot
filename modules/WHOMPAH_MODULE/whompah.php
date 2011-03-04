<?php

if (!function_exists('find_whompah_path')) {
	function find_whompah_path(&$queue, &$whompahs, &$endCity) {
		$current_whompah = array_shift($queue);
		
		if ($current_whompah == false) {
			return false;
		}
		
		if ($current_whompah->id == $endCity) {
			return $current_whompah;
		}
	
		forEach ($whompahs[$current_whompah->id]->connections as $city2_id) {
			if ($whompahs[$city2_id]->visited !== true) {
				$whompahs[$city2_id]->visited = true;
				$next_whompah = new stdClass;
				$next_whompah->id = $city2_id;
				$next_whompah->city_name = $whompahs[$city2_id]->city_name;
				$next_whompah->previous = &$current_whompah;
				$queue []= $next_whompah;
			}
		}
		
		return find_whompah_path($queue, $whompahs, $endCity);
	}
}

if (!function_exists('find_city')) {
	function find_city($search) {
		$db = DB::get_instance();
		
		$sql = "SELECT * FROM whompah_cities WHERE city_name LIKE '{$search}' OR short_name LIKE '{$search}'";
		$db->query($sql);
		$row = $db->fObject();
		if ($row === null) {
			return null;
		} else {
			return $row;
		}
	}
}

if (preg_match("/^whompah$/i", $message, $arr)) {
	$sql = "SELECT * FROM `whompah_cities` ORDER BY city_name ASC";
	$db->query($sql);
	$data = $db->fObject('all');

	$blob = "<header> :::::: Whompah Cities :::::: <end>\n\n<white>";
	forEach ($data as $row) {
		$blob .= "{$row->city_name} (<highlight>{$row->short_name}<end>)\n";
	}
	$blob .= "\nWritten By Tyrence (RK2)\nDatabase from a Bebot module written by POD13";
	
	$msg = Text::make_link('Whompah Cities', $blob, 'blob');
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^whompah (.+) (.+)$/i", $message, $arr)) {
	$startCity = find_city($arr[1]);
	$endCity = find_city($arr[2]);
	
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
	
	$whompahs = array();

	$sql = "SELECT * FROM `whompah_cities`";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$whompahs[$row->id] = $row;
	}
	
	$sql = "SELECT city1_id, city2_id FROM whompah_cities_rel";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$whompahs[$row->city1_id]->connections[] = $row->city2_id;
	}
	
	$whompah = new stdClass;
	$whompah->id = $endCity->id;
	$whompah->city_name = $whompahs[$endCity->id]->city_name;
	$whompah->previous = null;
	$whompah->visited = true;
	$obj = find_whompah_path($q = array($whompah), $whompahs, $startCity->id);
	
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
	$city = find_city($arr[1]);
	
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

<?php

if (!function_exists('find_whompa_path')) {
	function find_whompa_path(&$queue, &$whompas, &$endCity) {
		$current_whompa = array_shift($queue);
		
		if ($current_whompa == false) {
			return false;
		}
		
		if ($current_whompa->id == $endCity) {
			return $current_whompa;
		}
	
		forEach ($whompas[$current_whompa->id]->connections as $city2_id) {
			if ($whompas[$city2_id]->visited !== true) {
				$whompas[$city2_id]->visited = true;
				$next_whompa = new stdClass;
				$next_whompa->id = $city2_id;
				$next_whompa->city_name = $whompas[$city2_id]->city_name;
				$next_whompa->previous = &$current_whompa;
				$queue []= $next_whompa;
			}
		}
		
		return find_whompa_path($queue, $whompas, $endCity);
	}
}

if (!function_exists('find_city')) {
	function find_city($search) {
		$db = DB::get_instance();
		
		$sql = "SELECT * FROM whompa_cities WHERE city_name LIKE '{$search}' OR short_name LIKE '{$search}'";
		$db->query($sql);
		$row = $db->fObject();
		if ($row === null) {
			return null;
		} else {
			return $row;
		}
	}
}

if (preg_match("/^whompas?$/i", $message, $arr)) {
	$sql = "SELECT * FROM `whompa_cities` ORDER BY city_name ASC";
	$db->query($sql);
	$data = $db->fObject('all');

	$blob = "<header> :::::: Whompa Cities :::::: <end>\n\n<white>";
	forEach ($data as $row) {
		$blob .= "{$row->city_name} (<highlight>{$row->short_name}<end>)\n";
	}
	$blob .= "\nWritten By Tyrence (RK2)\nDatabase from a Bebot module written by POD13";
	
	$msg = Text::make_link('Whompa Cities', $blob, 'blob');
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^whompa (.+) (.+)$/i", $message, $arr)) {
	$startCity = find_city_id($arr[1]);
	$endCity = find_city_id($arr[2]);
	
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
	
	$whompas = array();

	$sql = "SELECT * FROM `whompa_cities`";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$whompas[$row->id] = $row;
	}
	
	$sql = "SELECT city1_id, city2_id FROM whompa_cities_rel";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$whompas[$row->city1_id]->connections[] = $row->city2_id;
	}
	
	$whompa = new stdClass;
	$whompa->id = $endCity->id;
	$whompa->city_name = $whompas[$endCity->id]->city_name;
	$whompa->previous = null;
	$whompa->visited = true;
	$obj = find_whompa_path($q = array($whompa), $whompas, $startCity->id);
	
	if ($obj === false) {
		$msg = "There was an error while trying to find the whompa path.";
	} else {
		while ($obj->previous !== null) {
			$msg .= "$obj->city_name -> ";
			$obj = &$obj->previous;
		}
		$msg .= "$obj->city_name";
	}
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^whompa (.+)$/i", $message, $arr)) {
	$city = find_city($arr[1]);
	
	if ($city === null) {
		$msg = "Error! Could not find city '$arr[1]'!";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$sql = "SELECT w2.* FROM whompa_cities_rel w1 JOIN whompa_cities w2 ON w1.city2_id = w2.id WHERE w1.city1_id = $city->id";
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

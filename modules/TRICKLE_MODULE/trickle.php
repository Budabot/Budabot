<?php

if (preg_match("/^trickle( ([a-zA-Z]+) ([0-9]+)){1,6}$/i", $message, $arr1) || preg_match("/^trickle( ([0-9]+) ([a-zA-Z]+)){1,6}$/i", $message, $arr2)) {
	$msg = "";
	
	$abilities = array('agi' => 0, 'int' => 0, 'psy' => 0, 'sta' => 0, 'str' => 0, 'sen' => 0);
	
	if ($arr1) {
		$array = explode(" ", $message);
		array_shift($array);
		for ($i = 0; isset($array[$i]); $i += 2) {
			$ability = Util::get_ability($array[$i]);
			if ($ability == null) {
				$syntax_error = true;
				return;
			}
			
			$abilities[$ability] = $array[1 + $i];
		}
	} else if ($arr2) {
		$array = explode(" ", $message);
		array_shift($array);
		for ($i = 0; isset($array[$i]); $i += 2) {
			$ability = Util::get_ability($array[1 + $i]);
			if ($ability == null) {
				$syntax_error = true;
				return;
			}
			
			$abilities[$ability] = $array[$i];
		}
	}

	$header = "";
	forEach ($abilities as $ability => $value) {
		if ($value != 0) {
			$header .= " (" . ucfirst($ability) . " " . $value . ")";
		}
	}

	$output = Text::make_header("Trickle$header", array('Help' => '/tell <myname> help trickle'));

	$results = getTrickleResults($abilities);
	$output .= formatOutput($results, $amount, $abilities);
	$output .= "\nBy Tyrence (RK2), inspired by the Bebot command of the same name";
	$msg = Text::make_blob("Trickle Results", $output);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

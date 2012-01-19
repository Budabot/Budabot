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
	} else {
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

	$blob = '';
	forEach ($abilities as $ability => $value) {
		if ($value != 0) {
			$blob .= ucfirst($ability) . " <highlight>" . $value . "<end>, ";
		}
	}
	$blob .= "\n";

	$results = getTrickleResults($abilities);
	$blob .= formatOutput($results, $amount, $abilities);
	$blob .= "\nBy Tyrence (RK2), inspired by the Bebot command of the same name";
	$msg = Text::make_blob("Trickle Results", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>

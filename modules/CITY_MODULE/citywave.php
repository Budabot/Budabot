<?php

if (preg_match("/^citywave$/i", $message)) {
	if (!isset($chatBot->data["CITY_WAVE"])) {
		$msg = "There is no raid in progress at this time.";
	} else if ($chatBot->data["CITY_WAVE"]['wave'] == 1) {
		$msg = "Waiting for the first wave.";
	} else {
		$msg = "Current wave: " . ($chatBot->data["CITY_WAVE"]['wave'] - 1);
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
<?php

if (preg_match("/^citywave$/i", $message)) {
	if (isset($chatBot->data["CITY_WAVE"])) {
		$msg = "Current wave: " . $chatBot->data["CITY_WAVE"]['wave'];
	} else {
		$msg = "There is no raid in progress at this time.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
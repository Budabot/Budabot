<?php

if (preg_match("/^startraid$/i", $message)) {
	if (isset($chatBot->data["CITY_WAVE"])) {
		$chatBot->send("A raid is already in progress.", "guild");
	} else {
		$chatBot->send("Wave counter started.", "guild");
		$chatBot->data["CITY_WAVE"]['time'] = time();
		$chatBot->data["CITY_WAVE"]['wave'] = 1;
	}
} else {
	$syntax_error = true;
}

?>
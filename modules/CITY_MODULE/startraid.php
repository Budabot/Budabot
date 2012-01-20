<?php

if (preg_match("/^startraid$/i", $message)) {
	if (isset($chatBot->data["CITY_WAVE"])) {
		$chatBot->sendGuild("A raid is already in progress.");
	} else {
		$chatBot->sendGuild("Wave counter started.");
		$chatBot->data["CITY_WAVE"]['time'] = time();
		$chatBot->data["CITY_WAVE"]['wave'] = 1;
	}
} else {
	$syntax_error = true;
}

?>
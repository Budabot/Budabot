<?php

if (preg_match("/^stopraid$/i", $message)) {
	unset($chatBot->data["CITY_WAVE"]);
	$chatBot->sendGuild("Wave counter stopped by $sender.");
} else {
	$syntax_error = true;
}

?>
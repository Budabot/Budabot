<?php

if (preg_match("/^stopraid$/i", $message)) {
	unset($chatBot->data["CITY_WAVE"]);
	$chatBot->send("Wave counter stopped by $sender.", "guild");
} else {
	$syntax_error = true;
}

?>
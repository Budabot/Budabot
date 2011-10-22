<?php

if (preg_match("/^Your city in (.+) has been targeted by hostile forces.$/i", $message)) {
	$chatBot->send("Wave counter started.", "guild");
	$chatBot->data["CITY_WAVE"]['time'] = time();
	$chatBot->data["CITY_WAVE"]['wave'] = 1;
}

?>

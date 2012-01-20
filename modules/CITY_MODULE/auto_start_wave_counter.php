<?php

if (preg_match("/^Your city in (.+) has been targeted by hostile forces.$/i", $message)) {
	$chatBot->sendGuild("Wave counter started.");
	$chatBot->data["CITY_WAVE"]['time'] = time();
	$chatBot->data["CITY_WAVE"]['wave'] = 1;
}

?>

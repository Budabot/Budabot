<?php
if (preg_match("/^Your city in (.+) has been targeted by hostile forces.$/i", $message, $arr)) {
	$chatBot->send("Wave counter started.", "guild");
	$chatBot->data["CITY_WAVE"]['time'] = time();
	$chatBot->data["CITY_WAVE"]['wave'] = 1;
} else if (preg_match("/^startraid/i", $message)) {
	if (isset($chatBot->data["CITY_WAVE"])) {
		$chatBot->send("A raid is already in progress.", "guild");
	} else {
		$chatBot->send("Wave counter started.", "guild");
		$chatBot->data["CITY_WAVE"]['time'] = time();
		$chatBot->data["CITY_WAVE"]['wave'] = 1;
	}
}
?>
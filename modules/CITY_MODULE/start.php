<?php
if (preg_match("/^Your city in (.+) has been targeted by hostile forces.$/i", $message, $arr)) {
	$chatBot->send("Wave counter started.", "guild");
	$this->data["CITY_WAVE"]['time'] = time();
	$this->data["CITY_WAVE"]['wave'] = 1;
} else if (preg_match("/^startraid/i", $message)) {
	if (isset($this->data["CITY_WAVE"])) {
		$chatBot->send("A raid is already in progress.", "guild");
	} else {
		$chatBot->send("Wave counter started.", "guild");
		$this->data["CITY_WAVE"]['time'] = time();
		$this->data["CITY_WAVE"]['wave'] = 1;
	}
}
?>
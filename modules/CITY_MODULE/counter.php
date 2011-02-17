<?php

if (isset($chatBot->data["CITY_WAVE"])) {
	$stime = $chatBot->data["CITY_WAVE"]['time'];
	$now = time();
	$wave = $chatBot->data["CITY_WAVE"]['wave'];
	if ($wave != 2) {
		if ($stime >= $now + 13 - $wave * 120 && $stime <= $now + 17 - $wave * 120) {
			if ($wave != 9) {
				$chatBot->send("Wave $wave Incoming.", "guild");
			} else {
				$chatBot->send("General Incoming.", "guild");
			}
			$wave++;
			$chatBot->data["CITY_WAVE"]['wave'] = $wave;
			if ($wave == 10) {
				// if raid is over, delete wave data
				unset($chatBot->data["CITY_WAVE"]);
			}
		}
	} elseif ($stime >= $now + 13 - 270 && $stime <= $now + 17 - 270) {
		$chatBot->send("Wave $wave Incoming.", "guild");
		$wave++;
		$chatBot->data["CITY_WAVE"]['wave'] = $wave;
	}
	if ($stime < $now - 10 * 120) {
		unset($chatBot->data["CITY_WAVE"]);
	}
} else {
	unset($chatBot->data["CITY_WAVE"]);
}

?>
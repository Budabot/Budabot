<?php

if (preg_match("/^bioinfo (.+) (\\d+)$/i", $message, $arr) ||
	preg_match("/^bioinfo (.+)$/i", $message, $arr)) {

	$bio = $arr[1];
	$ql = 300;
	if ($arr[2]) {
		$ql = $arr[2];
	}

	switch ($bio) {
		// ofab armor types
		case '64':
		case '295':
		case '468':
		case '935':
			$msg = ofabArmorBio($ql, $bio);
			$chatBot->send($msg, $sendto);
			return;

		// ofab weapon types
		case '18':
		case '34':
		case '687':
		case '812':
			$msg = ofabWeaponBio($ql, $bio);
			$chatBot->send($msg, $sendto);
			return;

		// AI types
		case "1":
		case "2":
		case "3":
		case "4":
		case "5":
		case "12":
		case "13":
		case "48":
		case "76":
		case "112":
		case "240":
		case "880":
		case "992":
			$msg = alienWeaponBio($ql, $bio);
			$chatBot->send($msg, $sendto);
			return;

		// other
		case "pristine":
		case "mutated":
			$msg = alienArmorBio($ql, $bio);
			$chatBot->send($msg, $sendto);
			return;

		case "serum":
			$msg = serumBio($ql, $bio);
			$chatBot->send($msg, $sendto);
			return;

		default:
			$chatBot->send("Unknown Bio-Material", $sendto);
			return;
	}
} else {
	$syntax_error = true;
}

?>
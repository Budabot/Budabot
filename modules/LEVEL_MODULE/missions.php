<?php

if (preg_match("/^(mission|missions) ([0-9]+)$/i", $message, $arr)) {
	$missionQl = $arr[2];

	if ($missionQl > 0 && $missionQl <= 250) {
		$msg = "QL{$missionQl} missions can be rolled from players who are these levels:";
	
		forEach (Level::find_all_levels() as $row) {
			$array = explode(",", $row->missions);
			if (in_array($missionQl, $array)) {
				$msg .= " " . $row->level;
			}
		}
	} else {
		$msg = "Missions are only available between QL1 and QL250";
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>

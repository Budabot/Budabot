<?php

// valid states for action are: 'on', 'off'
$db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 1 ");
if ($db->numrows() != 0) {
	$msg = "";
	$row = $db->fObject();

	if ($row->action == "off") {
		// 10 minutes before, send tell to player
		if ((time() - $row->time) >= 49*60 && (time() - $row->time) <= 50*60) {
			$msg = "The cloaking device is disabled. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to enable it.";
		} else if ((time() - $row->time) >= 58*60 && (time() - $row->time) <= 59*60) {
			// 1 minute before send tell to player
			$msg = "The cloaking device is disabled. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to enable it.";
		} else if ((time() - $row->time) >= 59*60 && ((time() - $row->time) % (60*5) >= 0 && (time() - $row->time) % (60*5) <= 60 )) {
			// when cloak can be raised, send tell to player and
			// every 5 minutes after, send tell to player
			$msg = "The cloaking device is disabled. Please enable it now.";
		}

		if ($msg) {
			// send message to all online alts
			$altInfo = Alts::get_alt_info($row->player);
			forEach ($altInfo->get_online_alts() as $name) {
				$chatBot->send($msg, $name);
			}
		}

		// send message to org chat every 5 minutes that the cloaking device is
		// disabled past the the time that the cloaking device could be enabled.
		$interval = 5;
		if ((time() - $row->time) >= 65*60 && ((time() - $row->time) % (60 * $interval) >= 0 && (time() - $row->time) % (60 * $interval) <= 60 )) {
			$timeString = Util::unixtime_to_readable(time() - $row->time, false);
			$chatBot->send("The cloaking device was disabled by <highlight>{$row->player}<end> $timeString ago. It is possible to enable it.", 'guild');
		}
	}
}

?>
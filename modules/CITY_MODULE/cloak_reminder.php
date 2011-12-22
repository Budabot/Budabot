<?php

// valid states for action are: 'on', 'off'
$row = $db->queryRow("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 1 ");
if ($row !== null) {
	$msg = "";

	if ($row->action == "off") {
		$timeSinceChange = time() - $row->time;
		$timeString = Util::unixtime_to_readable(3600 - $timeSinceChange, false);

		// 10 minutes before, send tell to player
		if ($timeSinceChange >= 49*60 && $timeSinceChange <= 50*60) {
			$msg = "The cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
		} else if ($timeSinceChange >= 58*60 && $timeSinceChange <= 59*60) {
			// 1 minute before send tell to player
			$msg = "The cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
		} else if ($timeSinceChange >= 59*60 && ($timeSinceChange % (60*5) >= 0 && $timeSinceChange % (60*5) <= 60 )) {
			// when cloak can be raised, send tell to player and
			// every 5 minutes after, send tell to player
			$msg = "The cloaking device is <orange>disabled<end>. Please enable it now.";
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
		$interval = Setting::get('cloak_reminder_interval');
		if ($timeSinceChange >= 65*60 && ($timeSinceChange % $interval >= 0 && $timeSinceChange % $interval <= 60 )) {
			$timeString = Util::unixtime_to_readable(time() - $row->time, false);
			$chatBot->send("The cloaking device was disabled by <highlight>{$row->player}<end> $timeString ago. It is possible to enable it.", 'guild');
		}
	}
}

?>
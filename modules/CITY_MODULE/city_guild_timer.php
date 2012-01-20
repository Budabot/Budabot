<?php

$row = $db->queryRow("SELECT * FROM org_city_<myname> ORDER BY `time` DESC LIMIT 1");
if ($row !== null) {
	$timeSinceChange = time() - $row->time;
    if ($row->action == "off") {
		// send message to org chat every 5 minutes that the cloaking device is
		// disabled past the the time that the cloaking device could be enabled.
		$interval = $setting->get('cloak_reminder_interval');
		if ($timeSinceChange >= 60*60 && ($timeSinceChange % $interval >= 0 && $timeSinceChange % $interval <= 60 )) {
			$timeString = Util::unixtime_to_readable(time() - $row->time, false);
			$chatBot->sendGuild("The cloaking device was disabled by <highlight>{$row->player}<end> $timeString ago. It is possible to enable it.");
		}
    } else if ($row->action == "on") {
        if ($timeSinceChange >= 60*60 && $timeSinceChange < 61*60) {
            $chatBot->sendGuild("The cloaking device was enabled one hour ago. Alien attacks can now be initiated.");
		}
    }
}

?>

<?php

if ($chatBot->is_ready() && isset($chatBot->guildmembers[$sender])) {
	$data = $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 0, 20 ");
    
    $case = 0;
    if (count($data) > 0) {
        $row = $data[0];
		$timeSinceChange = time() - $row->time;
		$timeString = Util::unixtime_to_readable(3600 - $timeSinceChange, false);

        if ($timeSinceChange >= 60*60 && $row->action == "off") {
	        $case = 1;
            $msg = "The cloaking device is <orange>disabled<end>. It is possible to enable it.";
    	} else if ($timeSinceChange < 60*30 && $row->action == "off") {
	    	$case = 1;
            $msg = "<red>RAID IN PROGRESS!  DO NOT ENTER CITY!</red>";
    	} else if ($timeSinceChange < 60*60 && $row->action == "off") {
            $msg = "Cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
            $case = 1;
    	} else if ($timeSinceChange >= 60*60 && $row->action == "on") {
            $msg = "The cloaking device is <green>enabled<end>. It is possible to disable it.";
            $case = 2;
    	} else if ($timeSinceChange < 60*60 && $row->action == "on") {
            $msg = "The cloaking device is <green>enabled<end>. It is possible in $timeString to disable it.";
            $case = 2;
    	} else {
			$msg = "<highlight>Unknown status on city cloak!<end>";
			$case = 1;
		}

		if ($case <= Setting::get("showcloakstatus")) {
			$chatBot->send($msg, $sender);
		}
    }
}

?>
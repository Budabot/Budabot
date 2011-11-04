<?php

if (preg_match("/^cloak$/i", $message)) {
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 20");
    if ($db->numrows() == 0) {
        $msg = "<highlight>Unknown status on cloak!<end>";
    } else {
		$row = $db->fObject();
		$timeSinceChange = time() - $row->time;
		$timeString = Util::unixtime_to_readable(3600 - $timeSinceChange, false);

        if ($timeSinceChange >= 3600 && $row->action == "off") {
            $msg = "The cloaking device is <orange>disabled<end>. It is possible to enable it.";
        } else if ($timeSinceChange < 3600 && $row->action == "off") {
            $msg = "The cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
        } else if ($timeSinceChange >= 3600 && $row->action == "on") {
            $msg = "The cloaking device is <green>enabled<end>. It is possible to disable it.";
		} else if ($timeSinceChange < 3600 && $row->action == "on") {
            $msg = "The cloaking device is <green>enabled<end>. It is possible in $timeString to disable it.";
		}

        $list = "<header> :::::: Cloak History :::::: <end>\n\n";
        $list .= "Time: <highlight>".date("M j, Y, G:i", $row->time)." (GMT)<end>\n";
        $list .= "Action: <highlight>Cloaking Device turned ".$row->action."<end>\n";
        $list .= "Player: <highlight>".$row->player."<end>\n\n";
        
        while ($row = $db->fObject()) {
            $list .= "Time: <highlight>".date("M j, Y, G:i", $row->time)." (GMT)<end>\n";
            $list .= "Action: <highlight>Cloaking Device turned ".$row->action."<end>\n";
            $list .= "Player: <highlight>".$row->player."<end>\n\n";
        }
        $msg .= " ".Text::make_blob("Cloak History", $list);
    }
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^cloak (raise|on)$/i", $message)) {
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 20");
	$row = $db->fObject();

	if ($row->action == "on") {
		$msg = "The cloaking device is already <green>enabled<end>.";
	} else {
		$db->exec("INSERT INTO org_city_<myname> (`time`, `action`, `player`) VALUES ('".time()."', 'on', '{$sender}*')");
		$msg = "The cloaking device has been manually enabled in the bot (you must still enable the cloak if it's disabled).";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
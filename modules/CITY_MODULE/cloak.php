<?php

if (preg_match("/^cloak$/i", $message)) {
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 0, 20 ");
    if ($db->numrows() == 0) {
        $msg = "<highlight>Unknown status on city cloak!<end>";
    } else {
		$timeSinceChange = time() - $row->time;
		$row = $db->fObject();
        if (($timeSinceChange >= 60*60) && ($row->action == "off")) {
            $msg = "The cloaking device is disabled. It is possible to enable it.";
        } else if (($timeSinceChange < 60*60) && ($row->action == "off")) {
            $msg = "The cloaking device is disabled. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to enable it.";
        } else if (($timeSinceChange >= 60*60) && ($row->action == "on")) {
            $msg = "The cloaking device is enabled. It is possible to disable it.";
		} else if (($timeSinceChange < 60*60) && ($row->action == "on")) {
            $msg = "The cloaking device is <green>enabled<end>. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to disable it.";
		}

        $list = "<header> :::::: City Cloak History :::::: <end>\n\n";
        $list .= "Time: <highlight>".gmdate("M j, Y, G:i", $row->time)." (GMT)<end>\n";
        if ($row->action == "Attack") {
            $list .= "Action: <highlight>City was under attack.<end>\n\n";
        } else if ($row->action == "on" || $row->action == "off") {
            $list .= "Action: <highlight>Cloaking Device has been turned ".$row->action."<end>\n";
            $list .= "Player: <highlight>".$row->player."<end>\n\n";
        }
        
        while ($row = $db->fObject()) {
            $list .= "Time: <highlight>".gmdate("M j, Y, G:i", $row->time)." (GMT)<end>\n";
            if ($row->action == "Attack") {
                $list .= "Action: <highlight>City was under attack.<end>\n\n";
            } else if ($row->action == "on" || $row->action == "off") {
                $list .= "Action: <highlight>Cloaking Device has been turned ".$row->action."<end>\n";
                $list .= "Player: <highlight>".$row->player."<end>\n\n";
            }
        }
        $msg .= " ".Text::make_blob("City History", $list);
    }
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^cloak (raise|on)$/i", $message)) {
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 0, 20 ");
	$row = $db->fObject();

	if ($row->action == "on") {
		$msg = "The cloaking device is already <green>enabled<end>.";
	} else {
		$db->exec("INSERT INTO org_city_<myname> (`time`, `action`, `player`) VALUES ('".time()."', 'on', '{$sender}*')");
		$msg = "The cloaking device has been manually set to on in the bot.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
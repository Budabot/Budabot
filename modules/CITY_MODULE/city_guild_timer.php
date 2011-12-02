<?php

$data = $db->query("SELECT * FROM org_city_<myname> ORDER BY `time` DESC LIMIT 1");
if (count($data) > 0) {
	$row = $data[0];
    if ($row->action == "off") {
        if (((time() - $row->time) >= 60*60) && ((time() - $row->time) < 61*60)) {
            $chatBot->send("The cloaking device was disabled one hour ago. It is now possible to enable it.", "guild");
		}
    } else if ($row->action == "on") {
        if (((time() - $row->time) >= 60*60) && ((time() - $row->time) < 61*60)) {
            $chatBot->send("The cloaking device was enabled one hour ago. Alien attacks can now be initiated.", "guild");
		}
    }
}

?>

<?php

$db->query("SELECT * FROM org_city_<myname> ORDER BY `time` DESC LIMIT 1");
if ($db->numrows() != 0) {
	$row = $db->fObject();
    if ($row->action == "off") {
        if (((time() - $row->time) >= 60*60) && ((time() - $row->time) < 61*60)) {
            $chatBot->send("Shields have been disabled one hour ago. It is now possible to enable it again.", "guild");
		}
    } else if ($row->action == "on") {
        if (((time() - $row->time) >= 60*60) && ((time() - $row->time) < 61*60)) {
            $chatBot->send("Shields have been enabled one hour ago. Alien attacks can be again initiated now.", "guild");
		}
    }
}
?>

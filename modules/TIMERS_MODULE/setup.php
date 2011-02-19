<?php
// Timer Table
$db->query("CREATE TABLE IF NOT EXISTS timers_<myname> (`name` VARCHAR(255), `owner` VARCHAR(25), `mode` VARCHAR(50), `timer` int, `settime` int, `callback` VARCHAR(255), `callback_param` VARCHAR(255))");

//Upload timers to memory
$db->query("SELECT * FROM timers_<myname>");
$data = $db->fObject('all');
$chatBot->data["timers"] = $data;

?>
<?php

//Upload timers to memory
$db->query("SELECT * FROM timers_<myname>");
$data = $db->fObject('all');
forEach ($data as $row) {
	$chatBot->data["timers"][strtolower($row->name)] = $row;
}

?>
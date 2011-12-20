<?php

//Upload timers to memory
$data = $db->query("SELECT * FROM timers_<myname>");
forEach ($data as $row) {
	$chatBot->data["timers"][strtolower($row->name)] = $row;
}

?>
<?php

//Upload broadcast bots to memory
$db->query("SELECT * FROM broadcast_<myname>");
$data = $db->fObject('all');
$chatBot->data["broadcast_list"] = array();
forEach ($data as $row) {
	$chatBot->data["broadcast_list"][$row->name] = $row;
}

?>
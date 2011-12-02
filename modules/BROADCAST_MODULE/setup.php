<?php

//Upload broadcast bots to memory
$data = $db->query("SELECT * FROM broadcast_<myname>");
$chatBot->data["broadcast_list"] = array();
forEach ($data as $row) {
	$chatBot->data["broadcast_list"][$row->name] = $row;
}

?>
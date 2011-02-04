<?php

//Upload broadcast bots to global vars
$db->query("SELECT * FROM broadcast_<myname>");
$data = $db->fObject('all');
$chatBot->vars["broadcast_list"] = array();
forEach ($data as $row) {
	$chatBot->vars["broadcast_list"][$row->name] = $row;
}

?>
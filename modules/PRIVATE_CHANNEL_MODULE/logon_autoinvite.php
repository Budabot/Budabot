<?php

$data = $db->query("SELECT * FROM members_<myname> WHERE name = '$sender' AND autoinv = 1");
if (count($data) != 0) {
    $msg = "You have been auto invited to the <highlight><myname><end> channel.";
    $chatBot->privategroup_invite($sender);
    $chatBot->send($msg, $sender);
}

?>
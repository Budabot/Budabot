<?php

if (isset($chatBot->data["ONLINE_MODULE"]) && $sender == $chatBot->data["ONLINE_MODULE"]['playername']) {
    if ($type == "logOn") {
		$status = "<green>online<end>";
	} else if ($type == "logOff") {
		$status = "<red>offline<end>";
	}
	$msg = "Player <highlight>$sender<end> is $status";
	$chatBot->send($msg, $chatBot->data["ONLINE_MODULE"]['sendto']);
	Buddylist::remove($sender, 'is_online');
	unset($chatBot->data["ONLINE_MODULE"]);
}

?>

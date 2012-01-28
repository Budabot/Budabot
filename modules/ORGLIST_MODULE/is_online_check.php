<?php

if (isset($chatBot->data["ONLINE_MODULE"]) && $sender == $chatBot->data["ONLINE_MODULE"]['playername']) {
    if ($type == "logon") {
		$status = "<green>online<end>";
	} else if ($type == "logoff") {
		$status = "<red>offline<end>";
	}
	$msg = "Character <highlight>$sender<end> is $status.";
	$chatBot->data["ONLINE_MODULE"]['sendto']->reply($msg);
	$buddylistManager->remove($sender, 'is_online');
	unset($chatBot->data["ONLINE_MODULE"]);
}

?>

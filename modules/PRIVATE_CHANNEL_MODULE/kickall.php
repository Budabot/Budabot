<?php

if (preg_match("/^kickall$/", $message)) {
	$msg = "Everyone will be kicked from this channel in 10 seconds. [by <highlight>$sender<end>]";
	$chatBot->sendPrivate($msg);
	$chatBot->data["priv_kickall"] = time() + 10;
	Registry::getInstance('eventManager')->activate("2sec", "PRIVATE_CHANNEL_MODULE/kickall_event.php");
} else {
	$syntax_error = true;
}

?>

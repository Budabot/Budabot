<?php

if ($type == "leavePriv") {
	if ($chatBot->data["leader"] == $sender) {
		unset($chatBot->data["leader"]);
	  	$msg = "Raid leader cleared.";
		$chatBot->send($msg, 'priv');
	}
}

?>
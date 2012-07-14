<?php

if ($type == "leavepriv") {
	if ($chatBot->data["leader"] == $sender) {
		unset($chatBot->data["leader"]);
		$msg = "Raid leader cleared.";
		$chatBot->sendPrivate($msg);
	}
}

?>

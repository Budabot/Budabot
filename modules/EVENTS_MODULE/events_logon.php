<?php

if ($chatBot->is_ready() && isset($chatBot->guildmembers[$sender])) {
	$msg = getEvents();
	if ($msg != '') {
		$chatBot->sendTell($msg, $sender);
	}
}

?>

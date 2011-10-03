<?php

if ($chatBot->is_ready()) {
	$msg = getEvents();
	if ($msg != '') {
		$chatBot->send($msg, $sender);
	}
}

?>

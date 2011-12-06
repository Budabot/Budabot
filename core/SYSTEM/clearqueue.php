<?php

if (preg_match("/^clearqueue$/i", $message)) {
	$num = 0;
	forEach ($chatBot->chatqueue->queue as $priority) {
		$num += count($priority);
	}
	$chatBot->chatqueue->queue = array();

	$chatBot->send("Chat queue has been cleared of $num messages.", $sendto);
}

?>

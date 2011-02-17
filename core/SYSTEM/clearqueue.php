<?php

if (preg_match("/^clearqueue$/i", $message)) {
	$chatBot->chatqueue->queue = array();

	$chatBot->send('Chat queue has been cleared.', $sendto);
}

?>

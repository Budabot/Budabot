<?php

if (preg_match("/^clearqueue$/i", $message)) {
	$this->chatqueue->queue = array();

	bot::send('Chat queue has been cleared.', $sendto);
}

?>

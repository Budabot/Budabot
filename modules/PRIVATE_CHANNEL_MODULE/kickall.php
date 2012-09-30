<?php

if (preg_match("/^kickall$/", $message)) {
	$msg = "Everyone will be kicked from this channel in 10 seconds. [by <highlight>$sender<end>]";
	$chatBot->sendPrivate($msg);
	$timer = Registry::getInstance('timer');
	$timer->callLater(10, array($chatBot, 'privategroup_kick_all'));
} else {
	$syntax_error = true;
}

?>

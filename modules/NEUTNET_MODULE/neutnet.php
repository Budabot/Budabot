<?php

if ($type == "msg" && preg_match("/^Neutnet[\\d]{1,2}$/", $sender)) {
	$this->send($message, 'guild');
	
	// keeps the bot from sending a message back to the neutnet satellite bot
	$sender = NULL;
}

?>
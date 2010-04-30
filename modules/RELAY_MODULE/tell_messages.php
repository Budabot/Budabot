<?php

if ($type == "msg" && strtolower($sender) == strtolower($this->settings["externalrelaybot"])) {

	// TODO process tell commands by bot
	
	// keeps the bot from sending a message back to relay bot
	$sender = NULL;
}

?>
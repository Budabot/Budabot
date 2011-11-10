<?php

if (ucfirst(strtolower(Setting::get('worldnet_bot'))) == $sender) {
	$msg = "!join";
	Logger::log_chat("Out. Msg.", $sender, $msg);
	$chatBot->send_tell($sender, $msg);
}

?>
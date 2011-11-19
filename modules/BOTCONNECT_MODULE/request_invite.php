<?php

if (Botconnect::onConnectList($sender)) {
	$msg = "!join";
	Logger::log_chat("Out. Msg.", $sender, $msg);
	$chatBot->send_tell($sender, $msg);
}

?>
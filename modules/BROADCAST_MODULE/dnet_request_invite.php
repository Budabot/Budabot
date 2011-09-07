<?php

if (Setting::get('dnet_status') == 1) {
	$name = "Dnetorg";
	$msg = "!join";
	Logger::log_chat("Out. Msg.", $name, $msg);
	$chatBot->send_tell($name, $msg);
}

?>
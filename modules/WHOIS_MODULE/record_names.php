<?php

if (($packet_type == AOCP_CLIENT_NAME || $packet_type == AOCP_CLIENT_LOOKUP) && Util::isValidSender($args[0])) {
	$chatBot->data['name_history_cache'] []= $args;
}

?>

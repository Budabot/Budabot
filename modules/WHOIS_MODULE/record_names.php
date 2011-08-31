<?php

if ($packet_type == AOCP_CLIENT_NAME || $packet_type == AOCP_CLIENT_LOOKUP) {
	$chatBot->data['name_history_cache'] []= $args;
}

?>

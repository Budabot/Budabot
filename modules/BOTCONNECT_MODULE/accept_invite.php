<?php

if (Botconnect::onConnectList($sender)) {
	$chatBot->privategroup_join($sender);
}

?>